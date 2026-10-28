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
        Schema::create('market_indices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('label', 80);
            $table->string('value_text', 60);
            $table->string('change_text', 60);
            $table->enum('direction', ['up', 'down'])->default('up');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('market_indices');
    }
};
