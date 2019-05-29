<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagesTable extends Migration
{
    private $tablename = 'images';
    
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->tablename, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('class')->comment('user defined class of image');
            $table->integer('imageable_id')->nullable()->comment('polymorphic relationship target id');
            $table->string('imageable_type')->nullable()->comment('polymorphic relationship table name');
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')->on('images')->onDelete('SET NULL');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists($this->tablename);
    }
}
