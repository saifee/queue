<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableCalls extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('calls', function (Blueprint $table) {
            $table->time('waiting_time')->nullable()->change();
            $table->time('served_time')->nullable()->change();
            $table->time('turn_around_time')->nullable()->change();
            $table->string('token_letter')->change();
            $table->unsignedBigInteger('call_status_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('calls', function (Blueprint $table) {
            $table->time('waiting_time')->change();
            $table->time('served_time')->change();
            $table->time('turn_around_time')->change();
            $table->integer('token_letter')->change();
            $table->unsignedBigInteger('call_status_id')->change();
        });
    }
}
