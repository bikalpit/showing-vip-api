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
            $table->string('property_id', 200);
            $table->enum('notification_email', ['YES', 'NO']);
            $table->enum('notification_text', ['YES', 'NO']);
            $table->enum('type', ['VALID', 'NO VALID'])->comment('VALID - validation | NO VALID - no validation');
            $table->string('validator', 200);
            $table->string('presence', 200);
            $table->longText('instructions');
            $table->string('lockbox_type', 200);
            $table->longText('lockbox_location');
            $table->date('start_date');
            $table->date('end_date');
            $table->time('timeframe');
            $table->enum('overlap', ['YES', 'NO']);
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
