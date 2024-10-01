<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('start')->nullable();
            $table->dateTime('end')->nullable();

            $table->string('name');
            $table->string('notes', 500)->nullable();
            $table->string('meet_with')->nullable();

            $table->string('location_name')->nullable();
            $table->unsignedBigInteger('location_id')->nullable();
            $table->foreign('location_id')->references('id')
                ->on('locations')->onDelete('set null');

            $table->json('reminders')->nullable();
            $table->boolean('recurring_active')->default(false);
            $table->string('repeat_options')->nullable();
            $table->json('end_date_options')->nullable();

            $table->unsignedBigInteger('player_id');
            $table->foreign('player_id')->references('id')
                ->on('players')->onDelete('cascade');

            $table->timestamps();
        });

        Schema::table('events', function (Blueprint $table) {
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->foreign('parent_id')->references('id')
                ->on('events')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
