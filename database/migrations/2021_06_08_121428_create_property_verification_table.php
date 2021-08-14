<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertyVerificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('property_verification', function (Blueprint $table) {
            $table->id();
            $table->string('property_id', 200);
            $table->string('agent_id', 200);
            $table->string('user_id', 200);
            $table->string('token', 200);
            $table->string('send_time', 200);
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
        Schema::dropIfExists('property_verification');
    }
}
