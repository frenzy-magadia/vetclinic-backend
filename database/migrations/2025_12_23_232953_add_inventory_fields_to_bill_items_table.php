<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bill_items', function (Blueprint $table) {
            $table->enum('item_type', ['service', 'inventory'])->default('service')->after('bill_id');
            $table->foreignId('inventory_item_id')->nullable()->after('item_type')->constrained('inventory_items')->onDelete('set null');
            $table->integer('quantity')->default(1)->after('inventory_item_id');
            $table->decimal('unit_price', 10, 2)->default(0)->after('quantity');
            
            // Rename amount column temporarily if it exists
            // The 'amount' column should now be auto-calculated
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bill_items', function (Blueprint $table) {
            $table->dropForeign(['inventory_item_id']);
            $table->dropColumn(['item_type', 'inventory_item_id', 'quantity', 'unit_price']);
        });
    }
};