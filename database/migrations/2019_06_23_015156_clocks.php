<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class Clocks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::create('clocks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->timestamp('timeIn')->nullable();
            $table->timestamp('timeOut')->nullable();
            $table->boolean('status')->default(1);
             $table->unsignedBigInteger('studentId');
            $table->foreign('studentId')->references('id')->on('students');
            $table->rememberToken();
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
        Schema::dropIfExists('clocks');
    }
}
