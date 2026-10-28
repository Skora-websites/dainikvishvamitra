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
        Schema::create('poll_options', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('poll_id');
            $table->string('option_text', 160);
            $table->unsignedInteger('votes')->default(0);
            $table->string('color_class', 30)->nullable()->default('bg-primary');
            $table->integer('sort_order')->default(0);

            $table->index('poll_id', 'idx_po_poll');
            $table->foreign('poll_id', 'fk_po_poll')->references('id')->on('polls')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('poll_options');
    }
};
