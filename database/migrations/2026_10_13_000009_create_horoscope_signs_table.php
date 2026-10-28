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
        Schema::create('horoscope_signs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_hi', 40);
            $table->string('name_en', 40);
            $table->string('symbol', 8);
            $table->text('prediction');
            $table->string('lucky_color', 40)->nullable();
            $table->string('lucky_number', 20)->nullable();
            $table->string('deity', 60)->nullable();
            $table->integer('sort_order')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horoscope_signs');
    }
};
