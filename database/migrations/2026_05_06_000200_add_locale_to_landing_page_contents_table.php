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
        if (! Schema::hasTable('landing_page_contents')) {
            return;
        }

        Schema::table('landing_page_contents', function (Blueprint $table): void {
            if (! Schema::hasColumn('landing_page_contents', 'locale')) {
                $table->string('locale', 5)->default('en')->after('id');
            }
        });

        Schema::table('landing_page_contents', function (Blueprint $table): void {
            $table->dropUnique('landing_page_contents_key_unique');
            $table->unique(['locale', 'key'], 'landing_page_contents_locale_key_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (! Schema::hasTable('landing_page_contents')) {
            return;
        }

        Schema::table('landing_page_contents', function (Blueprint $table): void {
            $table->dropUnique('landing_page_contents_locale_key_unique');
            $table->unique('key', 'landing_page_contents_key_unique');

            if (Schema::hasColumn('landing_page_contents', 'locale')) {
                $table->dropColumn('locale');
            }
        });
    }
};
