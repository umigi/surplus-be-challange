<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductImageTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('product_image', function (Blueprint $table) {
            $table->bigInteger('product_id')->unsigned();
            $table->bigInteger('image_id')->unsigned();

            $table->foreign('product_id')->references('id')->on('product')->onDelete('cascade');
            $table->foreign('image_id')->references('id')->on('image');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('product_image');
    }
}
