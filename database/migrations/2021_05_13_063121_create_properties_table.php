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
            $table->string('mls_name', 200);
            $table->longText('data');
            $table->enum('verified', ['YES', 'NO', 'VC'])->comment('YES - Verified | NO - Unverified | CV - Cancelled verification')->default('NO');
            $table->dateTime('last_update')->nullable();
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
