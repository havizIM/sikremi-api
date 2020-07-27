<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBuildingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('buildings', function (Blueprint $table) {
            $table->id();
            
            $table->index('partner_id');
            $table->foreignId('partner_id')->references('id')->on('partners')->unsigned();

            $table->index('city_id');
            $table->foreignId('city_id')->references('id')->on('cities')->unsigned();

            $table->index('province_id');
            $table->foreignId('province_id')->references('id')->on('provinces')->unsigned();

            $table->string('building_code', 20)->nullable();
            $table->string('building_name', 50);
            $table->text('type')->nullable();
            $table->text('address')->nullable();
            $table->string('phone', 15)->nullable();
            $table->string('fax', 15)->nullable();
            $table->string('email', 50)->nullable();
            $table->double('longitude', 15, 10)->nullable();
            $table->double('latitude', 15, 10)->nullable();
            $table->text('other_information')->nullable();

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
        Schema::dropIfExists('buildings');
    }
}
