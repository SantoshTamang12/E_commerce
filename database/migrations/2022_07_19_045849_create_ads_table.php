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
        Schema::create('ads', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id');
            $table->foreignUuid('category_id');
            $table->foreignUuid('sub_category_id');
            $table->string('title')->unique();
            $table->string('adId');
            $table->longText('description')->nullable();
            $table->longText('fields');
            $table->boolean('is_featured')->default(0);
            $table->integer('price')->nullable();
            $table->string('position')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->boolean('sold')->default(0);
            $table->datetime('expired_at')->nullable();
            $table->string('sold_to')->nullable();
            $table->string('status')->comment("['active', 'expired', 'inactive']");
            $table->timestamps();

            $table->foreign('sold_to')->references('id')->on('users');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ads');
    }
};
