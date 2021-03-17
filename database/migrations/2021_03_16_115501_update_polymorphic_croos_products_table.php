<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCrossProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cross_products', function (Blueprint $table) {
            $table->string('childable_type')->default('Aero\Catalog\Models\Product');
            $table->renameColumn('child_id', 'childable_id');

            $table->string('parentable_type')->default('Aero\Catalog\Models\Product');
            $table->renameColumn('parent_id', 'parentable_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cross_products', function(Blueprint $table) {

            $table->dropColumn('childable_type');
            $table->renameColumn('childable_id', 'child_id');

            $table->dropColumn('parentable_type');
            $table->renameColumn('parentable_id', 'parent_id');
        });
    }
}
