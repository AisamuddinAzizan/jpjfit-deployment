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
        Schema::create('health_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('test_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('height_cm', 5, 2);
            $table->decimal('weight_kg', 5, 2);
            $table->decimal('bmi', 5, 2);
            $table->unsignedSmallInteger('blood_pressure_systolic');
            $table->unsignedSmallInteger('blood_pressure_diastolic');
            $table->decimal('glucose_mmol', 5, 2)->nullable();
            $table->decimal('cholesterol_mmol', 5, 2)->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['participant_id', 'test_session_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('health_records');
    }
};
