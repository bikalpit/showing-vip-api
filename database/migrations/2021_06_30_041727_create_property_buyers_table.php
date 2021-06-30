<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertyBuyersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('property_buyers', function (Blueprint $table) {
            $table->id();
            $table->string('property_id', 200)->nullable();
            $table->string('seller_id', 200)->nullable();
            $table->string('buyer_id', 200)->nullable();
            $table->string('agent_id', 200)->nullable();
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
        Schema::dropIfExists('property_buyers');
    }
}
