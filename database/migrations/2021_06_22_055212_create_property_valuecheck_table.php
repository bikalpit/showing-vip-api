<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertyValuecheckTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('property_valuecheck', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 200);
            $table->string('property_id', 200);
            $table->string('vs_streetnumber', 200)->nullable();
            $table->string('vs_streetdirection', 200)->nullable();
            $table->string('vs_streetname', 200)->nullable();
            $table->string('vs_streettype', 200)->nullable();
            $table->string('vs_unitnumber', 200)->nullable();
            $table->string('vs_city', 200)->nullable();
            $table->string('vs_state', 200)->nullable();
            $table->string('vs_zipcode', 200)->nullable();
            $table->string('vs_county', 200)->nullable();
            $table->string('vs_countyname', 200)->nullable();
            $table->string('vs_country', 200)->nullable();
            $table->string('vs_apn', 200)->nullable();
            $table->string('vs_assessyr', 200)->nullable();
            $table->string('vs_assesmkt', 200)->nullable();
            $table->string('vs_landmktval', 200)->nullable();
            $table->string('vs_taxyr', 200)->nullable();
            $table->string('vs_taxdue', 200)->nullable();
            $table->string('vs_esttaxes', 200)->nullable();
            $table->string('vs_ownername', 200)->nullable();
            $table->string('vs_ownername2', 200)->nullable();
            $table->string('vs_formallegal', 1000)->nullable();
            $table->string('vs_saledate', 200)->nullable();
            $table->string('vs_docdate', 200)->nullable();
            $table->string('vs_saleamt', 200)->nullable();
            $table->string('vs_pricesqft', 200)->nullable();
            $table->string('vs_longitude', 200)->nullable();
            $table->string('vs_latitude', 200)->nullable();
            $table->string('vs_proptype', 200)->nullable();
            $table->string('vs_stories', 200)->nullable();
            $table->string('vs_housestyle', 200)->nullable();
            $table->string('vs_squarefeet', 200)->nullable();
            $table->string('vs_bsmtsf', 200)->nullable();
            $table->string('vs_finbsmtsf', 200)->nullable();
            $table->string('vs_bsmttype', 200)->nullable();
            $table->string('vs_bedrooms', 200)->nullable();
            $table->string('vs_bathrooms', 200)->nullable();
            $table->string('vs_garagetype', 200)->nullable();
            $table->string('vs_garagesqft', 200)->nullable();
            $table->string('vs_carspaces', 200)->nullable();
            $table->string('vs_fireplaces', 200)->nullable();
            $table->string('vs_heat', 200)->nullable();
            $table->string('vs_cool', 200)->nullable();
            $table->string('vs_extwall', 200)->nullable();
            $table->string('vs_roofcover', 200)->nullable();
            $table->string('vs_roofstyle', 200)->nullable();
            $table->string('vs_yearblt', 200)->nullable();
            $table->string('vs_lotsizec', 200)->nullable();
            $table->string('vs_lotsize', 200)->nullable();
            $table->string('vs_acre', 200)->nullable();
            $table->string('vs_pool', 200)->nullable();
            $table->string('vs_spa', 200)->nullable();
            $table->string('vs_foundation', 200)->nullable();
            $table->string('vs_golf', 200)->nullable();
            $table->string('vs_lotwidth', 200)->nullable();
            $table->string('vs_lotlength', 200)->nullable();
            $table->string('vs_waterfront', 200)->nullable();
            $table->string('vs_extwallcover', 200)->nullable();
            $table->string('vs_intwall', 200)->nullable();
            $table->string('vs_decksqft', 200)->nullable();
            $table->string('vs_deckdesc', 200)->nullable();
            $table->string('vs_patiosqft', 200)->nullable();
            $table->string('vs_patiodesc', 200)->nullable();
            $table->string('vs_waterservice', 200)->nullable();
            $table->string('vs_sewerservice', 200)->nullable();
            $table->string('vs_electricservice', 200)->nullable();
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
        Schema::dropIfExists('property_valuecheck');
    }
}
