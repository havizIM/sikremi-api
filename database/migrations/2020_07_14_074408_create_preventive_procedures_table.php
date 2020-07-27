<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePreventiveProceduresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('preventive_procedures', function (Blueprint $table) {
            $table->index('procedure_id');
            $table->foreignId('procedure_id')->references('id')->on('procedures')->unsigned();
            
            $table->text('description');
            $table->string('periode', 10)->nullable();
            $table->text('tools')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('preventive_procedures');
    }
}
