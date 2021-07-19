<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgentInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agent_info', function (Blueprint $table) {
            $table->id();
            $table->string('agent_id', 200);
            $table->dateTime('hmdo_lastupdated');
            $table->string('hmdo_mls_originator', 200);
            $table->string('hmdo_agent_name', 200);
            $table->string('hmdo_agent_title', 200);
            $table->longText('hmdo_agent_photo_url');
            $table->string('hmdo_agent_email', 200);
            $table->string('hmdo_office_main_phone', 200);
            $table->string('hmdo_office_direct_phone', 200);
            $table->string('hmdo_office_mobile_phone', 200);
            $table->longText('hmdo_agent_skills');
            $table->string('hmdo_office_id', 200);
            $table->string('hmdo_office_name', 200);
            $table->longText('hmdo_office_photo');
            $table->string('hmdo_office_street', 200);
            $table->string('hmdo_office_city', 200);
            $table->string('hmdo_office_zipcode', 200);
            $table->string('hmdo_office_state', 200);
            $table->string('hmdo_office_phone', 200);
            $table->string('hmdo_office_website', 200);
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
        Schema::dropIfExists('agent_info');
    }
}
