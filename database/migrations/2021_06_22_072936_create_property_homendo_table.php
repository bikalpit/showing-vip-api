<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertyHomendoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('property_homendo', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 200)->nullable();
            $table->string('property_id', 200)->nullable();
            $table->string('hmdo_listed', 200)->nullable()
            $table->string('hmdo_lastupdated', 200)->nullable();
            $table->string('hmdo_mls_id', 200)->nullable();
            $table->string('hmdo_mls_originator', 200)->nullable();
            $table->string('hmdo_mls_proptype', 200)->nullable();
            $table->string('hmdo_mls_propname', 200)->nullable();
            $table->string('hmdo_mls_status', 200)->nullable();
            $table->string('hmdo_mls_price', 200)->nullable();
            $table->string('hmdo_mls_streetnumber', 200)->nullable();
            $table->string('hmdo_mls_streetdirection', 200)->nullable();
            $table->string('hmdo_mls_streetname', 200)->nullable();
            $table->string('hmdo_mls_streettype', 200)->nullable();
            $table->string('hmdo_mls_unitnumber', 200)->nullable();
            $table->string('hmdo_mls_city', 200)->nullable();
            $table->string('hmdo_mls_zipcode', 200)->nullable();
            $table->string('hmdo_mls_state', 200)->nullable();
            $table->string('hmdo_mls_latitude', 200)->nullable();
            $table->string('hmdo_mls_longitude', 200)->nullable();
            $table->string('hmdo_mls_yearbuilt', 200)->nullable();
            $table->string('hmdo_mls_beds', 200)->nullable();
            $table->string('hmdo_mls_baths', 200)->nullable();
            $table->string('hmdo_mls_sqft', 200)->nullable();
            $table->string('hmdo_mls_acres', 200)->nullable();
            $table->string('hmdo_mls_carspaces', 200)->nullable();
            $table->string('hmdo_mls_url', 1000)->nullable();
            $table->string('hmdo_mls_thumbnail', 1000)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('property_homendo');
    }
}
