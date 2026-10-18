<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->string('size'); // e.g. 6, 7, 8, 9, 10, 11
            $table->string('color'); // e.g. Obsidian, Ivory, Sand
            $table->string('color_hex', 20)->default('#171717');
            $table->string('sku')->unique();
            $table->integer('stock_quantity')->default(0);
            $table->decimal('price_adjustment', 10, 2)->default(0.00);
            $table->timestamps();

            $table->index(['product_id', 'size', 'color']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
