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
            $table->string('tracking_id')->unique();
            $table->string('container_number')->nullable()->index();
            // A client who still owns shipments cannot be deleted by accident.
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('origin');
            $table->string('destination');
            $table->string('current_location')->nullable();
            $table->string('cargo_description')->nullable();
            $table->decimal('weight_kg', 10, 2)->nullable();
            $table->string('status')->default('pending')->index();
            $table->date('eta')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
