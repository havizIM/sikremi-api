<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEquipmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('equipments', function (Blueprint $table) {
            $table->id();

            $table->index('building_id');
            $table->foreignId('building_id')->references('id')->on('buildings')->unsigned();

            $table->index('category_id');
            $table->foreignId('category_id')->references('id')->on('categories')->unsigned();

            $table->index('procedure_id');
            $table->foreignId('procedure_id')->references('id')->on('procedures')->unsigned();

            $table->string('sku', 20);
            $table->string('equipment_name', 50);
            $table->text('brand')->nullable();
            $table->text('type')->nullable();
            $table->text('location')->nullable();
            $table->text('other_information')->nullable();
            $table->text('photo')->nullable();

            $table->timestamps();
            $table->softDeletes(); // deleted_at
            $table->integer('created_by')->nullable();
            $table->integer('updated_by')->nullable();
            $table->integer('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('equipments');
    }
}
