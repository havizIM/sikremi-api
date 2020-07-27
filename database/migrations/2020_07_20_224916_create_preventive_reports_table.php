<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreventiveReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preventive_reports', function (Blueprint $table) {
            $table->id();

            $table->index('schedule_id');
            $table->foreignId('schedule_id')->references('id')->on('schedules')->unsigned();

            $table->index('equipment_id');
            $table->foreignId('equipment_id')->references('id')->on('equipments')->unsigned();

            $table->string('report_number', 15);
            $table->date('date');
            $table->text('note')->nullable();
            $table->text('signature')->nullable();
            $table->text('approved_by')->nullable();

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
        Schema::dropIfExists('preventive_reports');
    }
}
