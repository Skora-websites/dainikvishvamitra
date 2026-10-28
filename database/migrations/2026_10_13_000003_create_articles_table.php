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
        Schema::create('articles', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('category_id');
            $table->string('title', 255);
            $table->string('slug', 220)->unique();
            $table->text('excerpt')->nullable();
            $table->mediumText('body')->nullable();
            $table->string('image', 255)->nullable();
            $table->string('image_alt', 255)->nullable();
            $table->string('image_caption', 255)->nullable();
            $table->string('author_name', 120)->nullable()->default('विशेष संवाददाता ब्यूरो');
            $table->string('author_role', 160)->nullable();
            $table->string('author_image', 255)->nullable();
            $table->string('location', 120)->nullable();
            $table->string('badge_label', 80)->nullable();
            $table->string('badge_color', 30)->default('bg-primary');
            $table->string('stat_text', 120)->nullable();
            $table->boolean('is_breaking')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_opinion')->default(false);
            $table->boolean('is_visual')->default(false);
            $table->string('home_slot', 20)->nullable()->comment('hero|spot|market|pol_lead|pol_list|sport_lead|sport_list|ent|real');
            $table->integer('home_order')->default(0);
            $table->string('cat_slot', 20)->nullable()->comment('lead|grid|dossier');
            $table->integer('cat_order')->default(0);
            $table->string('time_label', 40)->nullable()->comment('fixed relative-time label, e.g. ४० मिनट पूर्व');
            $table->integer('read_minutes')->default(4);
            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('comment_count')->default(0);
            $table->enum('status', ['published', 'draft'])->default('draft');
            $table->dateTime('published_at')->nullable();
            $table->string('meta_title', 255)->nullable();
            $table->string('meta_desc', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->useCurrent();

            $table->index(['category_id', 'status', 'published_at'], 'idx_cat_status');
            $table->index(['status', 'is_featured', 'is_breaking'], 'idx_flags');
            $table->foreign('category_id', 'fk_art_cat')->references('id')->on('categories')->restrictOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articles');
    }
};
