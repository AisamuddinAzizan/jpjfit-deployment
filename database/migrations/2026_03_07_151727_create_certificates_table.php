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
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('test_session_id')->constrained()->cascadeOnDelete();
            $table->string('certificate_no')->unique();
            $table->timestamp('issued_at')->nullable();
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('pdf_path')->nullable();
            $table->timestamps();

            $table->unique(['participant_id', 'test_session_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
