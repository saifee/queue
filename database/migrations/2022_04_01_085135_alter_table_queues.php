<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableQueues extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('queues', function (Blueprint $table) {
            $table->string('name')->nullable()->after('letter');
            $table->string('email')->nullable()->after('name');
            $table->string('phone')->nullable()->after('email');
            $table->integer('position')->nullable()->after('phone');
        });
    }

    /**
     * Reverse the migrations.
     *
     *
     * @return void
     */
    public function down()
    {
        Schema::table('queues', function (Blueprint $table) {
            $table->dropColumn('name');
            $table->dropColumn('email');
            $table->dropColumn('phone');
            $table->dropColumn('position');
        });
    }
}
