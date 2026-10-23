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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('tagline')->nullable();
            $table->text('description');
            $table->unsignedInteger('price');
            $table->unsignedInteger('sale_price')->nullable();
            $table->string('type')->default('ready_stock');
            $table->string('baking_schedule')->nullable();
            $table->integer('stock')->default(0);
            $table->string('image')->nullable();
            $table->string('badge')->nullable();
            $table->text('ingredients')->nullable();
            $table->unsignedInteger('weight_grams')->default(100);
            $table->decimal('rating', 3, 2)->default(4.90);
            $table->unsignedInteger('review_count')->default(25);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
