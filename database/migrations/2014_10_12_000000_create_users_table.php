<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->uuid('id')->primary();            
            $table->string('name')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->unique();
            $table->string('device_token')->unique()->nullable();
            $table->string('social_unique_id')->unique()->nullable();
            $table->string('social_provider')->nullable();
            $table->datetime('dob')->nullable();
            $table->string('gender' )->comment('[
                \'Male\',
                \'Female\',
                \'Other\',
                \'Unspecified\'
            ]')->default('Unspecified');
            $table->string('avatar_url')->nullable();
            $table->string('location')->nullable();
            $table->double('latitude', 15, 8)->nullable();
            $table->double('longitude',15,8)->nullable();
            $table->mediumInteger('otp')->nullable();
            $table->timestamp('otp_verified_at')->nullable();
            $table->timestamp('otp_sent_at')->nullable();
            $table->string('status')->comment('active,  inactive, banned, suspended')->default('inactive');
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
};
