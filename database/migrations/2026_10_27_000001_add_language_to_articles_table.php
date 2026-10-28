<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The article form has always offered a हिंदी / English choice, but the
     * column was never added, so the selection was silently discarded on
     * save. Articles are single-language; mixed-script copy (an English term
     * inside a Hindi sentence) is still allowed within one article.
     */
    public function up(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->enum('language', ['hi', 'en'])->default('hi')->after('title');
            $table->index('language', 'idx_articles_language');
        });
    }

    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropIndex('idx_articles_language');
            $table->dropColumn('language');
        });
    }
};
