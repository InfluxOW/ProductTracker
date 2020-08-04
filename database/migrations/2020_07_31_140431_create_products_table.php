<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('retailer_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('price')->nullable();
            $table->string('sku');
            $table->string('url')->nullable();
            $table->boolean('in_stock')->nullable();
            $table->unique(['name', 'sku', 'retailer_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
