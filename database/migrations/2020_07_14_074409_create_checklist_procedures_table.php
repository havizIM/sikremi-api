<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChecklistProceduresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('checklist_procedures', function (Blueprint $table) {
            $table->index('procedure_id');
            $table->foreignId('procedure_id')->references('id')->on('procedures')->unsigned();
            
            $table->text('description');
            $table->text('parameter');
            $table->enum('format', ['Number', 'Text', 'Option'])->default('Text');
            $table->text('subparameter')->nullable();
            $table->string('unit', 30)->nullable();
            $table->string('periode', 10)->nullable();
            $table->text('tools')->nullable();
            $table->text('normal_limit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('checklist_procedures');
    }
}
