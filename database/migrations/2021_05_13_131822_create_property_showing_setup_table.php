<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertyShowingSetupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('property_showing_setup', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 200);
            $table->string('property_id', 200)->nullable();
            $table->enum('notification_email', ['YES', 'NO'])->default('NO');
            $table->enum('notification_text', ['YES', 'NO'])->default('NO');
            $table->enum('type', ['VALID', 'NO VALID'])->comment('VALID - validation | NO VALID - no validation')->default('NO VALID');
            $table->string('validator', 200)->nullable();
            $table->string('presence', 200)->nullable();
            $table->longText('instructions')->nullable();
            $table->string('lockbox_type', 200)->nullable();
            $table->longText('lockbox_location')->nullable();
            $table->string('lockbox_code', 200)->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('timeframe', 10)->nullable();
            $table->enum('overlap', ['YES', 'NO'])->default('NO');
            $table->dateTime('cancel_at')->nullable();
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
        Schema::dropIfExists('property_showing_setup');
    }
}
