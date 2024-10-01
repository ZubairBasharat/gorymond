<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('players', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('pin', 10)->nullable();
            $table->string('phone', 25)->nullable();

            $table->string('email')->unique();
            $table->boolean('reminder_alerts')->default(false);
            $table->smallInteger('event_display_number')->nullable();


            $table->string('avatar')->nullable();
            $table->string('api_token', 80)
                ->unique()
                ->nullable()
                ->default(null);

            $table->string('push_token')
                ->unique()
                ->nullable();

            $table->unsignedBigInteger('coach_id');
            $table->foreign('coach_id')->references('id')
                ->on('users')->onDelete('cascade');

            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();

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
        Schema::dropIfExists('players');
    }
}
