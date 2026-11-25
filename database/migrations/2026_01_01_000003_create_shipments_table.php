<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->string('shipment_number', 50)->unique(); // e.g. "BIO-2026-0042"
            $table->foreignId('customer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('carrier_id')->nullable()->constrained('carriers')->nullOnDelete();
            $table->foreignId('origin_id')->constrained('locations')->cascadeOnUpdate();
            $table->foreignId('destination_id')->constrained('locations')->cascadeOnUpdate();
            
            // Biomedical Cold Chain specifics
            $table->enum('storage_temp', ['CRYO_LN2', 'DRY_ICE', 'CHILLED'])->default('DRY_ICE');
            $table->enum('status', [
                'REGISTERED',
                'CRYOPACKED',
                'PICKED_UP',
                'IN_TRANSIT',
                'CUSTOMS_HOLD',
                'OUT_FOR_DELIVERY',
                'DELIVERED',
                'CANCELLED'
            ])->default('REGISTERED');
            
            $table->decimal('total_weight_kg', 8, 2)->default(5.00);
            $table->dateTime('expected_departure');
            $table->dateTime('expected_arrival');
            $table->dateTime('estimated_arrival');
            
            // Delay & Viability calculation
            $table->boolean('is_delayed')->default(false);
            $table->integer('delay_hours')->default(0);
            $table->enum('delay_severity', ['NONE', 'LOW', 'MEDIUM', 'HIGH'])->default('NONE');
            $table->string('delay_reason')->nullable();
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};

