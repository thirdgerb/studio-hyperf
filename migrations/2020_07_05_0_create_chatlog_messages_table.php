<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;
use Commune\Chatlog\Database\ChatlogMessageRepo;

class CreateChatlogMessagesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(
            ChatlogMessageRepo::TABLE_NAME,
            function (Blueprint $table) {
                ChatlogMessageRepo::createTable($table);
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(ChatlogMessageRepo::TABLE_NAME);
    }
}
