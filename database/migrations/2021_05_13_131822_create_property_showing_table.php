<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertyShowingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('property_showing', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 200);
            $table->string('property_id', 200);
            $table->enum('notification_email', ['YES', 'NO']);
            $table->enum('notification_text', ['YES', 'NO']);
            $table->enum('showing_type', ['VALID', 'NO VALID'])->comment('VALID - validation | NO VALID - no validation');
            $table->string('showing_validator', 200);
            $table->string('showing_presence', 200);
            $table->longText('showing_instructions');
            $table->string('lockbox_type', 200);
            $table->longText('lockbox_location');
            $table->date('showing_start_date');
            $table->date('showing_end_date');
            $table->time('showing_timeframe');
            $table->enum('showing_overlap', ['YES', 'NO']);
            $table->dateTime('cancel_at');
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
        Schema::dropIfExists('property_showing');
    }
}
