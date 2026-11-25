<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action'); // e.g. "CREATE_SHIPMENT", "UPDATE_STATUS", "RECORD_EVENT", "RESOLVE_EXCEPTION"
            $table->string('entity_type'); // e.g. "Shipment", "ShipmentException"
            $table->unsignedBigInteger('entity_id');
            $table->json('old_payload')->nullable();
            $table->json('new_payload')->nullable();
            $table->string('ip_address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};

