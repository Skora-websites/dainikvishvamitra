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
        Schema::create('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name_hi', 120);
            $table->string('slug', 140)->unique();
            $table->string('description', 255)->nullable();
            $table->string('accent_color', 20)->default('bg-primary');
            $table->text('pills')->nullable()->comment('comma-separated sub-category pills');
            $table->integer('sort_order')->default(0);
            $table->boolean('show_in_nav')->default(true);
            $table->boolean('show_in_footer')->default(false);
            $table->enum('status', ['active', 'hidden'])->default('active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
