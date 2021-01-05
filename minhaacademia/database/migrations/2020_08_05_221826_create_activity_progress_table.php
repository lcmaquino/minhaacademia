<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivityProgressTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user');
            $table->foreignId('activity');

            $table->foreign('user')->references('id')->on('users')->onDelete('CASCADE');
    		$table->foreign('activity')->references('id')->on('activities')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity_progress');
    }
}
