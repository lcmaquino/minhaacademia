<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCoursesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->json('icon')->nullable();
            $table->string('title');
            $table->string('video')->nullable();
            $table->string('description', 5000)->nullable();
            $table->unsignedTinyInteger('duration')->default(0);
            $table->enum('visibility', ['0', '1'])->default(0);
            $table->foreignId('teacher');
            $table->timestamps();

            $table->foreign('teacher')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('courses');
    }
}
