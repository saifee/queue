<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAppVersionColumnToSettings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->string('app_version')->nullable()->after('installed');
            $table->boolean('print_preview_enabled')->default(true)->after('app_version');
            $table->string('display_voice_enabled')->default(true)->after('print_preview_enabled');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('app_version');
            $table->dropColumn('print_preview_enabled');
            $table->dropColumn('display_voice_enabled');
        });
    }
}
