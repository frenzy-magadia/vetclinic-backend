<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Step 1: Create the new inventory_batches table
        Schema::create('inventory_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained('inventory_items')->onDelete('cascade');
            $table->string('batch_number');
            $table->integer('quantity')->default(0);
            $table->date('expiry_date')->nullable();
            $table->date('manufacture_date')->nullable();
            $table->timestamps();
            
            $table->index(['inventory_item_id', 'expiry_date']);
        });

        // Step 2: Migrate existing inventory items to batches
        // Get all existing inventory items with their current stock
        $items = DB::table('inventory_items')->get();
        
        foreach ($items as $item) {
            if ($item->current_stock > 0) {
                // Create a batch for each existing item with stock
                DB::table('inventory_batches')->insert([
                    'inventory_item_id' => $item->id,
                    'batch_number' => $item->batch_number ?? 'LEGACY-' . $item->id,
                    'quantity' => $item->current_stock,
                    'expiry_date' => $item->expiry_date,
                    'manufacture_date' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // Step 3: Add inventory_batch_id to inventory_transactions table
        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->foreignId('inventory_batch_id')->nullable()->after('inventory_item_id')->constrained('inventory_batches')->onDelete('set null');
        });

        // Step 4: Remove old columns from inventory_items (but keep them temporarily with a rename)
        Schema::table('inventory_items', function (Blueprint $table) {
            // Rename instead of drop, so we can recover if needed
            $table->renameColumn('current_stock', 'current_stock_old');
            $table->renameColumn('batch_number', 'batch_number_old');
            $table->renameColumn('expiry_date', 'expiry_date_old');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Step 1: Restore old columns in inventory_items
        Schema::table('inventory_items', function (Blueprint $table) {
            $table->renameColumn('current_stock_old', 'current_stock');
            $table->renameColumn('batch_number_old', 'batch_number');
            $table->renameColumn('expiry_date_old', 'expiry_date');
        });

        // Step 2: Remove inventory_batch_id from inventory_transactions
        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->dropForeign(['inventory_batch_id']);
            $table->dropColumn('inventory_batch_id');
        });

        // Step 3: Drop inventory_batches table
        Schema::dropIfExists('inventory_batches');
    }
};