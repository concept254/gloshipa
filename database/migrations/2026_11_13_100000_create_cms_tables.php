<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Website content tables (Phase 2). Purely additive: nothing here touches the
 * users, shipments or shipment_events tables, so tracking keeps working.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $t) {
            $t->string('key')->primary();
            $t->text('value')->nullable();
            $t->timestamps();
        });

        Schema::create('pages', function (Blueprint $t) {
            $t->id();
            $t->string('slug')->unique();
            $t->string('title');
            $t->string('subtitle', 400)->nullable();
            $t->longText('body')->nullable();
            $t->string('header_image')->nullable();
            $t->string('meta_description', 300)->nullable();
            $t->string('kind', 20)->default('content'); // content = free page, system = has its own route
            $t->boolean('is_published')->default(true);
            $t->boolean('show_in_menu')->default(false);
            $t->boolean('show_in_footer')->default(false);
            $t->unsignedInteger('sort_order')->default(100);
            $t->timestamps();
        });

        Schema::create('services', function (Blueprint $t) {
            $t->id();
            $t->string('slug')->unique();
            $t->string('title');
            $t->string('summary', 400)->nullable();
            $t->longText('body')->nullable();
            $t->string('icon', 30)->default('box');
            $t->string('image')->nullable();
            $t->boolean('is_active')->default(true);
            $t->unsignedInteger('sort_order')->default(100);
            $t->timestamps();
        });

        Schema::create('faqs', function (Blueprint $t) {
            $t->id();
            $t->string('category', 80)->default('General');
            $t->string('question');
            $t->text('answer');
            $t->boolean('is_active')->default(true);
            $t->unsignedInteger('sort_order')->default(100);
            $t->timestamps();
        });

        Schema::create('testimonials', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('role')->nullable();
            $t->string('company')->nullable();
            $t->text('quote');
            $t->unsignedTinyInteger('rating')->default(5);
            $t->string('photo')->nullable();
            $t->boolean('is_active')->default(true);
            $t->unsignedInteger('sort_order')->default(100);
            $t->timestamps();
        });

        Schema::create('team_members', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('role');
            $t->text('bio')->nullable();
            $t->string('photo')->nullable();
            $t->string('linkedin_url')->nullable();
            $t->boolean('is_active')->default(true);
            $t->unsignedInteger('sort_order')->default(100);
            $t->timestamps();
        });

        Schema::create('partners', function (Blueprint $t) {
            $t->id();
            $t->string('name');
            $t->string('logo')->nullable();
            $t->string('url')->nullable();
            $t->boolean('is_active')->default(true);
            $t->unsignedInteger('sort_order')->default(100);
            $t->timestamps();
        });

        Schema::create('posts', function (Blueprint $t) {
            $t->id();
            $t->string('slug')->unique();
            $t->string('title');
            $t->string('excerpt', 400)->nullable();
            $t->longText('body')->nullable();
            $t->string('image')->nullable();
            $t->boolean('is_published')->default(true);
            $t->timestamp('published_at')->nullable();
            $t->timestamps();
        });

        Schema::create('inquiries', function (Blueprint $t) {
            $t->id();
            $t->string('type', 20)->default('contact'); // contact | quote
            $t->string('name');
            $t->string('email');
            $t->string('phone', 60)->nullable();
            $t->string('subject')->nullable();
            $t->text('message')->nullable();
            $t->json('meta')->nullable();
            $t->timestamp('read_at')->nullable();
            $t->timestamps();
        });

        Schema::create('subscribers', function (Blueprint $t) {
            $t->id();
            $t->string('email')->unique();
            $t->string('token', 64)->unique();
            $t->timestamp('subscribed_at')->nullable();
            $t->timestamp('unsubscribed_at')->nullable();
            $t->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['subscribers', 'inquiries', 'posts', 'partners', 'team_members', 'testimonials', 'faqs', 'services', 'pages', 'settings'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
