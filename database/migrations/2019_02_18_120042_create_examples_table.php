<?php

use App\Models\Example;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

use Illuminate\Database\Migrations\Migration;

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
            $table->boolean('active')->default(Example::ACTIVE);
            $table->enum('status', Example::STATUSES)->default(Example::STATUS_1);
            $table->string('title', 100);
            $table->longText('description');
            $table->string('photo')->nullable();
            $table->string('photo_resize')->nullable();
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
