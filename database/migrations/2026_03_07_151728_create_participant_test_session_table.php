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
        Schema::create('participant_test_session', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('test_session_id')->constrained()->cascadeOnDelete();
            $table->enum('attendance_status', ['registered', 'attended', 'absent'])->default('registered');
            $table->enum('result_status', ['pending', 'pass', 'fail'])->default('pending');
            $table->timestamps();

            $table->unique(['participant_id', 'test_session_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participant_test_session');
    }
};
