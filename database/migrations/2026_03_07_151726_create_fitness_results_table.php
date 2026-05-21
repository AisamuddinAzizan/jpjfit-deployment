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
        Schema::create('fitness_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('test_session_id')->constrained()->cascadeOnDelete();
            $table->foreignId('recorded_by')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedSmallInteger('push_ups');
            $table->unsignedSmallInteger('sit_ups');
            $table->decimal('sit_and_reach_cm', 5, 2);
            $table->decimal('shuttle_run_level', 5, 2);
            $table->unsignedInteger('run_2_4km_seconds');
            $table->decimal('total_score', 6, 2);
            $table->enum('classification', ['Poor', 'Average', 'Good', 'Excellent']);
            $table->enum('result_status', ['Pass', 'Fail']);
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->unique(['participant_id', 'test_session_id']);
            $table->index(['classification', 'result_status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fitness_results');
    }
};
