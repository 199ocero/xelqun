<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The Shop and Blog areas of the public demo.
     */
    public function up(): void
    {
        Schema::create('brands', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('website')->nullable();
            $table->string('color')->default('#0071e3');
            $table->text('description')->nullable();
            $table->boolean('is_visible')->default(true);
            $table->timestamps();
        });

        Schema::create('customers', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->date('birthday')->nullable();
            $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();
            $table->string('tier')->default('standard');
            $table->timestamps();
        });

        Schema::table('products', function (Blueprint $table): void {
            $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
            $table->text('description')->nullable();
            $table->json('specs')->nullable();
            $table->json('keywords')->nullable();
            $table->json('gallery')->nullable();
            $table->string('color')->nullable();
            $table->unsignedTinyInteger('discount')->default(0);
            $table->dateTime('restock_at')->nullable();
            $table->time('pickup_time')->nullable();
        });

        Schema::table('categories', function (Blueprint $table): void {
            $table->unsignedInteger('sort')->default(0);
        });

        Schema::create('authors', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->text('bio')->nullable();
            $table->string('github')->nullable();
            $table->timestamps();
        });

        Schema::create('posts', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('author_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('body')->nullable();
            $table->json('blocks')->nullable();
            $table->text('snippet')->nullable();
            $table->string('status')->default('draft');
            $table->string('audience')->default('everyone');
            $table->json('topics')->nullable();
            $table->json('meta')->nullable();
            $table->string('accent')->nullable();
            $table->string('banner')->nullable();
            $table->unsignedTinyInteger('rating')->nullable();
            $table->boolean('is_pinned')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::create('comments', function (Blueprint $table): void {
            $table->id();
            $table->morphs('commentable');
            $table->string('name');
            $table->text('body');
            $table->string('status')->default('pending');
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
        });

        Schema::create('demo_mails', function (Blueprint $table): void {
            $table->id();
            $table->string('inbox')->index();
            $table->string('recipient');
            $table->string('subject');
            $table->longText('html');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demo_mails');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('posts');
        Schema::dropIfExists('authors');

        Schema::table('categories', function (Blueprint $table): void {
            $table->dropColumn('sort');
        });

        Schema::table('products', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('brand_id');
            $table->dropColumn(['description', 'specs', 'keywords', 'gallery', 'color', 'discount', 'restock_at', 'pickup_time']);
        });

        Schema::dropIfExists('customers');
        Schema::dropIfExists('brands');
    }
};
