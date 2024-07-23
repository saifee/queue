<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTableServices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->boolean('sms_enabled')->default(false)->after('status');
            $table->boolean('optin_message_enabled')->default(false)->after('sms_enabled');
            $table->boolean('call_message_enabled')->default(false)->after('optin_message_enabled');
            $table->boolean('noshow_message_enabled')->default(false)->after('call_message_enabled');
            $table->boolean('completed_message_enabled')->default(false)->after('noshow_message_enabled');
            $table->boolean('status_message_enabled')->default(false)->after('completed_message_enabled');
            $table->string('optin_message_format')->nullable()->after('status_message_enabled');
            $table->string('call_message_format')->nullable()->after('optin_message_format');
            $table->string('noshow_message_format')->nullable()->after('call_message_format');
            $table->string('completed_message_format')->nullable()->after('noshow_message_format');
            $table->string('status_message_format')->nullable()->after('completed_message_format');
            $table->string('status_message_positions')->nullable()->after('status_message_format');
            $table->boolean('ask_name')->default(false)->after('status_message_positions');
            $table->boolean('name_required')->default(false)->after('ask_name');
            $table->boolean('ask_email')->default(false)->after('name_required');
            $table->boolean('email_required')->default(false)->after('ask_email');
            $table->boolean('ask_phone')->default(false)->after('email_required');
            $table->boolean('phone_required')->default(false)->after('ask_phone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('sms_enabled');
            $table->dropColumn('optin_message_enabled');
            $table->dropColumn('call_message_enabled');
            $table->dropColumn('noshow_message_enabled');
            $table->dropColumn('completed_message_enabled');
            $table->dropColumn('optin_message_format');
            $table->dropColumn('call_message_format');
            $table->dropColumn('noshow_message_format');
            $table->dropColumn('completed_message_format');
            $table->dropColumn('ask_name');
            $table->dropColumn('name_required');
            $table->dropColumn('ask_email');
            $table->dropColumn('email_required');
            $table->dropColumn('ask_phone');
            $table->dropColumn('phone_required');
            $table->dropColumn('status_message_enabled');
            $table->dropColumn('status_message_format');
            $table->dropColumn('status_message_positions');
        });
    }
}
