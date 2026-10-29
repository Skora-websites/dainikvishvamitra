<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * English mirrors of the article content so one article can be written
     * in both Hindi (title/excerpt/body) and English (title_en/excerpt_en/body_en).
     */
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->string('title_en')->nullable()->after('title');
            $table->text('excerpt_en')->nullable()->after('excerpt');
            $table->text('body_en')->nullable()->after('body');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['title_en', 'excerpt_en', 'body_en']);
        });
    }
};
