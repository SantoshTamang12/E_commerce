<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserAdConversationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_ad_conversations', function (Blueprint $table) {
            $table->id();
            $table->uuid('buyer_id')->nullable()->onDelete('set null');
            $table->uuid('seller_id')->nullable()->onDelete('set null');
            $table->uuid('ad_id')->nullable()->onDelete('set null');
            $table->uuid('conversation_id')->nullable()->onDelete('set null');
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
        Schema::dropIfExists('user_ad_conversations');
    }
}
