<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrossProductsPresetRecommendedTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cross_products_preset_recommended', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('cross_products_preset_id');
            $table->unsignedInteger('recommended_id');
            $table->timestamps();

            $table->unique(['cross_products_preset_id', 'recommended_id'], 'crsel_reccom_unique');

            $table->foreign('cross_products_preset_id', 'crsel_reccom_preset')->references('id')->on('cross_products_presets')->onDelete('cascade');
            $table->foreign('recommended_id', 'crsel_reccom_product')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cross_products_preset_recommended');
    }
}
