<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('language_id')->nullable()->constrained('languages');
            $table->string('name');
            $table->text('address');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->string('location');
            $table->string('timezone');
            $table->string('logo')->nullable();
            $table->text('display_notification')->nullable();
            $table->integer('display_font_size')->nullable();
            $table->string('display_font_color')->nullable();
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
        Schema::drop('settings');
    }
}
