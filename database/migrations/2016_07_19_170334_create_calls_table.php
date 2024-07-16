<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCallsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('queue_id')->constrained('queues');
            $table->foreignId('service_id')->constrained('services');
            $table->foreignId('counter_id')->constrained('counters');
            $table->foreignId('user_id')->constrained('users');
            $table->integer('token_letter');
            $table->integer('token_number');
            $table->date('called_date');
            $table->timestamp('started_at');
            $table->timestamp('ended_at')->nullable();
            $table->time('waiting_time');
            $table->time('served_time');
            $table->time('turn_around_time');
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
        Schema::drop('calls');
    }
}
