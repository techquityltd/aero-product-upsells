<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrossProductsPresetProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cross_products_preset_product', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cross_products_preset_id');
            $table->unsignedInteger('product_id');
            $table->timestamps();

            $table->unique(['cross_products_preset_id', 'product_id'], 'cross_products_preset_id_product_id_unique');

            $table->foreign('cross_products_preset_id')->references('id')->on('cross_products_presets')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cross_products_preset_product');
    }
}
