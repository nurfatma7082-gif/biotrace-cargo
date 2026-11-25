<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('facility_name'); // e.g. "RSUP Nasional Dr. Cipto Mangunkusumo", "Changi Airport Cargo Center"
            $table->string('facility_code', 30)->unique(); // e.g. "RSCM-JKT", "SIN-CHG-AIR"
            $table->enum('facility_type', ['HOSPITAL', 'RESEARCH_LAB', 'AIRPORT_HUB', 'BIOBANK'])->default('HOSPITAL');
            $table->string('city');
            $table->string('country');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};

