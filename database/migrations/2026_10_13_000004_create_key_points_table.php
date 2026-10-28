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
        Schema::create('key_points', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('article_id');
            $table->text('point');
            $table->integer('sort_order')->default(0);

            $table->index('article_id', 'idx_kp_art');
            $table->foreign('article_id', 'fk_kp_art')->references('id')->on('articles')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('key_points');
    }
};
