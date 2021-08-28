<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->bigInteger("discord_id");
            $table->integer("gold");
            $table->integer("level");
            $table->bigInteger("exp");
            $table->integer("health");
            $table->integer("energy");
            $table->integer("helmet")->nullable();
            $table->integer("chestplate")->nullable();
            $table->integer("leggings")->nullable();
            $table->integer("boots")->nullable();
            $table->integer("weapon")->nullable();
            $table->integer("strength");
            $table->integer("dexterity");
            $table->integer("intelligence");
            $table->integer("gathering");
            $table->integer("luck");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
