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
        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->string('participant_no')->unique();
            $table->string('full_name');
            $table->string('ic_no')->unique();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female']);
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('agency')->nullable();
            $table->string('rank')->nullable();
            $table->text('address')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['full_name', 'agency']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participants');
    }
};
