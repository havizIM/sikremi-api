<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCorrectiveSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corrective_schedules', function (Blueprint $table) {
            $table->index('schedule_id');
            $table->foreignId('schedule_id')->references('id')->on('schedules')->unsigned();

            $table->index('work_order_id');
            $table->foreignId('work_order_id')->references('id')->on('work_orders')->unsigned();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('corrective_schedules');
    }
}
