<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Add your new columns here
            $table->string('timezone')->nullable();
            $table->boolean('email_notifications')->default(false);
            $table->boolean('email_opt_in')->default(false);
            $table->boolean('sms_notifications')->default(false);
            $table->boolean('sms_opt_in')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('timezone');
            $table->boolean('email_notifications');
            $table->boolean('email_opt_in');
            $table->boolean('sms_notifications');
            $table->boolean('sms_opt_in');

        });
    }
};
