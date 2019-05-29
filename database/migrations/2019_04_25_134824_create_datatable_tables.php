<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDatatableTables extends Migration
{
    protected $tablePrefix = 'dt_';
    
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create($this->tablePrefix . 'primary', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 100);
            $table->integer('parent_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create($this->tablePrefix . 'child', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 100);
            $table->integer('parent_id');
            $table->timestamps();
            $table->softDeletes();
        });
        Schema::create($this->tablePrefix . 'parent', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title', 100);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists($this->tablePrefix . 'primary');
        Schema::dropIfExists($this->tablePrefix . 'child');
        Schema::dropIfExists($this->tablePrefix . 'parent');
    }
}
