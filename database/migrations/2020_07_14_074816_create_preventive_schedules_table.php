<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreventiveSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preventive_schedules', function (Blueprint $table) {
            $table->index('schedule_id');
            $table->foreignId('schedule_id')->references('id')->on('schedules')->unsigned();

            $table->index('equipment_id');
            $table->foreignId('equipment_id')->references('id')->on('equipments')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('preventive_schedules');
    }
}
