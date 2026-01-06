<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('clinic_details', function (Blueprint $table) {
            $table->id();
            $table->string('clinic_name')->default('PetPro Veterinary Clinic Co.');
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->text('address')->nullable();
            $table->string('facebook')->nullable();
            
            // Business Hours (stored as JSON)
            $table->json('business_hours')->nullable();
            
            $table->timestamps();
        });

        Schema::create('clinic_services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('price_range');
            $table->string('icon')->default('fas fa-paw');
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default data
        DB::table('clinic_details')->insert([
            'clinic_name' => 'PetPro Veterinary Clinic Co.',
            'phone' => '0917-321-1830',
            'email' => 'petpro@gmail.com',
            'address' => 'UNIT A & B, Adora BLDG, Malabanban Sur, Candelaria',
            'facebook' => 'PetPro Veterinary Clinic',
            'business_hours' => json_encode([
                'weekdays' => ['start' => '08:00', 'end' => '18:00', 'label' => 'Monday - Friday'],
                'saturday' => ['start' => '09:00', 'end' => '16:00', 'label' => 'Saturday'],
                'sunday' => ['start' => '10:00', 'end' => '14:00', 'label' => 'Sunday'],
                'emergency' => '24/7 Available'
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Insert default services
        $services = [
            ['name' => 'Consultation', 'description' => 'Professional veterinary consultation for your pet\'s health', 'price_range' => '₱500', 'icon' => 'fas fa-stethoscope', 'order' => 1],
            ['name' => 'Surgery', 'description' => 'Advanced surgical procedures by experienced surgeons', 'price_range' => '₱2,000 - ₱15,000', 'icon' => 'fas fa-scalpel', 'order' => 2],
            ['name' => 'Vaccination', 'description' => 'Complete vaccination programs to protect your pet', 'price_range' => '₱300 - ₱1,500', 'icon' => 'fas fa-syringe', 'order' => 3],
            ['name' => 'Deworming', 'description' => 'Parasite prevention and treatment programs', 'price_range' => '₱200 - ₱800', 'icon' => 'fas fa-pills', 'order' => 4],
            ['name' => 'Laboratory', 'description' => 'Comprehensive diagnostic testing and lab services', 'price_range' => '₱500 - ₱3,000', 'icon' => 'fas fa-flask', 'order' => 5],
            ['name' => 'Pharmacy', 'description' => 'Full-service pharmacy with quality medications', 'price_range' => 'Varies by medication', 'icon' => 'fas fa-prescription-bottle-alt', 'order' => 6],
            ['name' => 'Grooming', 'description' => 'Professional grooming services for your pet', 'price_range' => '₱350 - ₱1,200', 'icon' => 'fas fa-cut', 'order' => 7],
            ['name' => 'Boarding', 'description' => 'Safe and comfortable boarding facilities', 'price_range' => '₱300 - ₱600/day', 'icon' => 'fas fa-home', 'order' => 8],
            ['name' => 'Dog & Cat Food', 'description' => 'Premium quality pet food and nutrition products', 'price_range' => '₱150 - ₱2,500', 'icon' => 'fas fa-bone', 'order' => 9],
        ];

        foreach ($services as $service) {
            DB::table('clinic_services')->insert(array_merge($service, [
                'created_at' => now(),
                'updated_at' => now(),
            ]));
        }
    }

    public function down()
    {
        Schema::dropIfExists('clinic_services');
        Schema::dropIfExists('clinic_details');
    }
};