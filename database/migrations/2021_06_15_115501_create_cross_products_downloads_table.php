<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCrossProductsDownloadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cross_product_downloads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('location');
            $table->json('collections');
            $table->unsignedInteger('admin_id')->nullable();
            $table->boolean('complete')->default(false);
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cross_product_downloads');
    }
}
