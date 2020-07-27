<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreventiveReportDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preventive_report_details', function (Blueprint $table) {
            $table->index('preventive_report_id');
            $table->foreignId('preventive_report_id')->references('id')->on('preventive_reports')->unsigned();

            $table->text('description');
            $table->string('periode', 10)->nullable();
            $table->text('tools')->nullable();
            $table->enum('check', ['Y', 'N'])->default('N');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('preventive_report_details');
    }
}
