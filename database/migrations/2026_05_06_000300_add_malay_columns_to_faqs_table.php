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
        if (! Schema::hasTable('faqs')) {
            return;
        }

        Schema::table('faqs', function (Blueprint $table): void {
            if (! Schema::hasColumn('faqs', 'question_ms')) {
                $table->string('question_ms')->nullable()->after('question');
            }

            if (! Schema::hasColumn('faqs', 'answer_ms')) {
                $table->text('answer_ms')->nullable()->after('answer');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('faqs')) {
            return;
        }

        Schema::table('faqs', function (Blueprint $table): void {
            if (Schema::hasColumn('faqs', 'question_ms')) {
                $table->dropColumn('question_ms');
            }

            if (Schema::hasColumn('faqs', 'answer_ms')) {
                $table->dropColumn('answer_ms');
            }
        });
    }
};
