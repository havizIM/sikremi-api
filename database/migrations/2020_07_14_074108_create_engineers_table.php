<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEngineersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('engineers', function (Blueprint $table) {
            $table->id();

            $table->index('user_id');
            $table->foreignId('user_id')->references('id')->on('users')->unsigned();

            $table->index('city_id');
            $table->foreignId('city_id')->references('id')->on('cities')->unsigned();

            $table->index('province_id');
            $table->foreignId('province_id')->references('id')->on('provinces')->unsigned();

            $table->string('full_name', 50);
            $table->text('address')->nullable();
            $table->string('phone', 12)->nullable();
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
        Schema::dropIfExists('engineers');
    }
}
