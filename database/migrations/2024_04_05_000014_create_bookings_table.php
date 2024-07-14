<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('fullname');
            $table->string('phone');
            $table->string('email');
            $table->unsignedBigInteger('table_id')->nullable();
            $table->foreign('table_id')->references('id')->on('tables');
            $table->datetime('start_book');
            $table->datetime('finish_book');
            $table->string('status')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
