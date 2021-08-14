<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePropertyOwnersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('property_owners', function (Blueprint $table) {
            $table->id();
            $table->string('property_id', 200);
            $table->string('user_id', 200);
            $table->enum('type', ['main_owner', 'sub_owner'])->default('sub_owner');
            $table->string('verification_token', 200)->nullable();
            $table->enum('verify_status', ['YES', 'NO', 'VC'])->comment('YES - Verified | NO - Unverified | CV - Cancelled verification')->default('NO');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('property_owners');
    }
}
