<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\Models\Example;

class CreateExamplesTable extends Migration
{
    protected $table = 'examples';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->softDeletes();
            $table->integer('created_by');
            $table->enum('active', [
                Example::ACTIVE, 
                Example::INACTIVE
            ])->default(Example::ACTIVE);
            $table->enum('status', Example::STATUSES)->default(Example::STATUS_1);
            $table->string('title', 100);
            $table->longText('description');
            $table->string('photo')->nullable();
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
        Schema::dropIfExists($this->table);
    }
}