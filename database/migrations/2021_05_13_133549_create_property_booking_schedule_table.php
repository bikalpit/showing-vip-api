<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertyBookingScheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('property_booking_schedule', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 200);
            $table->string('buyer_id', 200)->nullable();
            $table->string('property_id', 200);
            $table->string('agent_id', 200)->nullable();
            $table->date('booking_date');
            $table->string('booking_time', 100);
            $table->enum('status', ['P', 'A', 'R', 'NA'])->comment('P - Pending | A - Approved | R - Reject | NA - Not Approved');
            $table->enum('cv_status', ['on-hold', 'verify'])->comment('on-hold - User not verified | verify - User verified');
            $table->string('cancel_by', 200)->nullable();
            $table->string('cancel_reason', 200)->nullable();
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
        Schema::dropIfExists('property_booking_schedule');
    }
}
