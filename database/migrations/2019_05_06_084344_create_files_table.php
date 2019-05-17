<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFilesTable extends Migration
{
    private $tablename = 'files';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->char('disk', 60)->nullable()->index()->comment('the disk on which the file is stored');
            $table->char('directory', 255)->nullable()->index()->comment('the directory in which the file is stored');
            $table->string('name')->nullable();
            $table->char('class', 60)->nullable()->index()->comment('user defined class of file');
            $table->integer('size')->nullable()->comment('the size of file in bytes');
            $table->char('mime', 60)->nullable()->index()->comment('file mime type');
            $table->string('description')->nullable();
            $table->integer('fileable_id')->nullable()->comment('polymorphic relationship target id');
            $table->string('fileable_type')->nullable()->comment('polymorphic relationship table name');
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
