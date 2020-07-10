<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJobSubtasksFailuresTable extends Migration
{
    private $tablename = 'job_subtasks_failures';
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('job_status_id');
            $table->unsignedInteger('subtask_id');
            $table->text('main_error_message');
            $table->longText('all_errors_messages')->nullable();
            $table->longText('subtask_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->tablename);
    }
}
