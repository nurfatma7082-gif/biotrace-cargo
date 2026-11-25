<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_exceptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->enum('exception_type', [
                'TEMPERATURE_EXCURSION',
                'DRY_ICE_DEPLETION',
                'CUSTOMS_BIOHAZARD_HOLD',
                'WEATHER_DELAY',
                'CONTAINER_DAMAGE',
                'DOCUMENTATION_ISSUE'
            ]);
            $table->enum('severity', ['LOW', 'MEDIUM', 'HIGH'])->default('MEDIUM');
            $table->enum('status', ['OPEN', 'INVESTIGATING', 'RESOLVED'])->default('OPEN');
            $table->dateTime('detected_at');
            $table->dateTime('resolved_at')->nullable();
            $table->text('description');
            $table->text('resolution_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_exceptions');
    }
};

