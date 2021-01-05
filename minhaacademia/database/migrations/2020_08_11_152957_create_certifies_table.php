<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCertifiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('certifies', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cpf', 11);
            $table->string('title');
            $table->unsignedTinyInteger('duration')->default(0);
            $table->string('code', 8);
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
        Schema::dropIfExists('certifies');
    }
}
