<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Social Media Links table
        Schema::create('social_media_links', function (Blueprint $table) {
            $table->id();
            $table->string('platform'); // facebook, instagram, twitter, youtube, etc.
            $table->string('name'); // Display name
            $table->string('url');
            $table->string('icon')->nullable(); // Font Awesome icon class
            $table->string('color')->nullable(); // Brand color
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // Legal Pages table (Privacy Policy, Terms, etc.)
        Schema::create('pages', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->longText('content');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('show_in_footer')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('social_media_links');
        Schema::dropIfExists('pages');
    }
};
