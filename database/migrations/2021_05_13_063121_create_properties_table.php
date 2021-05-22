<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 200);
            $table->string('mls_id', 200);
            $table->enum('verified', ['P', 'VS', 'V'])->comment('P - pending | VS - verification send | V - verified');
            $table->string('title', 200);
            $table->string('type', 200);
            $table->string('size', 200);
            $table->string('status', 200);
            $table->string('year_built', 200);
            $table->string('lat_area', 200);
            $table->string('elementary', 200);
            $table->string('middle', 200);
            $table->string('high', 200);
            $table->string('district', 200);
            $table->string('phone', 200);
            $table->string('office', 200);
            $table->string('hoa', 200);
            $table->string('taxes', 200);
            $table->string('parking', 200);
            $table->string('sources', 200);
            $table->string('disclaimer', 200);
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
        Schema::dropIfExists('properties');
    }
}
