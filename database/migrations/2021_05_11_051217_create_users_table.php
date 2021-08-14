<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('uuid', 200);
            $table->string('first_name', 200)->nullable();
            $table->string('last_name', 200)->nullable();
            $table->string('phone', 200)->nullable();
            $table->string('email', 200);
            $table->string('password', 200)->nullable();
            $table->enum('role', ['USER', 'AGENT', 'SA'])->comment('USER - Buyer or Seller | AGENT - Agent | SA - Super Admin');
            $table->enum('sub_role', ['SELLER', 'BUYER'])->comment('SELLER - Seller | BUYER - Buyer')->nullable();
            $table->enum('agent_role', ['LIST', 'BUY', 'SHOW'])->comment('LIST - Listing | BUY - Buying | SHOW - Showing')->nullable();
            $table->string('mls_id', 200)->nullable();
            $table->string('mls_name', 200)->nullable();
            $table->string('phone_verification_token', 200)->nullable();
            $table->enum('phone_verified', ['YES', 'NO']);
            $table->string('email_verification_token', 200)->nullable();
            $table->enum('email_verified', ['YES', 'NO']);
            $table->string('verification_token', 200)->nullable();
            $table->enum('verify_status', ['YES', 'NO', 'VC'])->comment('YES - Verified | NO - Unverified | CV - Cancelled verification')->default('NO');
            $table->string('ip_address', 200)->nullable();
            $table->string('image', 200);
            $table->string('address', 200)->nullable();
            $table->string('city', 200)->nullable();
            $table->string('zipcode', 200)->nullable();
            $table->string('state', 200)->nullable();
            $table->string('country', 200)->nullable();
            $table->longText('about')->nullable();
            $table->string('website_url', 500)->nullable();
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
        Schema::dropIfExists('users');
    }
}
