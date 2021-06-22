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
            $table->string('uuid', 200);
            $table->string('property_id', 200);
            $table->string('hmdo_listed', 200);
            $table->string('hmdo_lastupdated', 200);
            $table->string('hmdo_mls_id', 200);
            $table->string('hmdo_mls_originator', 200);
            $table->string('hmdo_mls_proptype', 200);
            $table->string('hmdo_mls_propname', 200);
            $table->string('hmdo_mls_status', 200);
            $table->string('hmdo_mls_price', 200);
            $table->string('hmdo_mls_url', 1000);
            $table->string('hmdo_mls_thumbnail', 1000);
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
