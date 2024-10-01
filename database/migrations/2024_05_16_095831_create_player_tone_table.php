<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlayerToneTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('player_tones', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('situation_type')->nullable();
            $table->tinyInteger('activity_type')->nullable();
            $table->foreignId('player_id')->constrained()->onDelete('cascade');
            $table->foreignId('tone_id')->constrained()->onDelete('cascade');
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
        Schema::dropIfExists('player_tones');
    }
}