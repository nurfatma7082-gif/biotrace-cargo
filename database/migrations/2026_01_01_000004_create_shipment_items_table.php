<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipment_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id')->constrained('shipments')->cascadeOnDelete();
            $table->string('specimen_type'); // e.g. "Umbilical Cord Stem Cells", "BRCA1 Genomic DNA Extract", "Biopsy Tissue"
            $table->string('specimen_identifier', 50)->nullable(); // e.g. "VIAL-BATCH-994"
            $table->integer('quantity_vials')->default(1);
            $table->enum('biohazard_level', ['BSL_1', 'BSL_2', 'BSL_3'])->default('BSL_2');
            $table->decimal('required_temp_celsius', 5, 2)->default(-80.00); // e.g. -196.00, -80.00, 4.00
            $table->text('storage_instructions')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipment_items');
    }
};

