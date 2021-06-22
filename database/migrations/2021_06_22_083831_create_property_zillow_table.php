<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertyZillowTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('property_zillow', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 200);
            $table->string('property_id', 200);
            $table->string('z_zpid', 200);
            $table->string('z_sale_amount', 200);
            $table->string('z_sale_lowrange', 200);
            $table->string('z_sale_highrange', 200);
            $table->string('z_sale_lastupdated', 200);
            $table->string('z_rental_amount', 200);
            $table->string('z_rental_lowrange', 200);
            $table->string('z_rental_highrange', 200);
            $table->string('z_rental_lastupdated', 200);
            $table->string('z_prop_url', 1000);
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
        Schema::dropIfExists('property_zillow');
    }
}
