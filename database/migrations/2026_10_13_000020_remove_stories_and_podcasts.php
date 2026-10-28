<?php

use Illuminate\Database\Migrations\Migration;

/**
 * Drops the Visual stories (visual_stories) and Podcasts (podcast_episodes)
 * features at the user's request. Destructive: run once on existing installs.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        // Both are standalone content tables with no inbound FKs.
        Schema::dropIfExists('visual_stories');
        Schema::dropIfExists('podcast_episodes');

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        Schema::enableForeignKeyConstraints();

        Schema::create('visual_stories', function ($table) {
            $table->increments('id');
            $table->string('title', 160);
            $table->string('image', 255)->nullable();
            $table->string('badge_label', 60)->nullable();
            $table->string('badge_color', 30)->default('bg-primary');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
        });

        Schema::create('podcast_episodes', function ($table) {
            $table->increments('id');
            $table->string('title', 255);
            $table->string('host', 120)->default('विश्वामित्र पॉडकास्ट डेस्क');
            $table->string('audio_url', 500);
            $table->string('cover_image', 255)->nullable();
            $table->string('duration_text', 20)->nullable();
            $table->text('description')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->dateTime('published_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['is_active', 'sort_order', 'published_at'], 'idx_pod_active');
        });

        Schema::disableForeignKeyConstraints();
    }
};
