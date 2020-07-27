<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();

            $table->index('building_id');
            $table->foreignId('building_id')->references('id')->on('buildings')->unsigned();

            $table->date('date');
            $table->time('time')->nullable();
            $table->text('estimate')->nullable();
            $table->enum('type', ['Preventive', 'Corrective', 'Checklist'])->default('Preventive');
            $table->string('shift')->nullable();
            $table->text('description')->nullable();
            $table->enum('submit', ['Y', 'N'])->default('N');

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
        Schema::dropIfExists('schedules');
    }
}
