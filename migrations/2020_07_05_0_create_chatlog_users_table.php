<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;
use Commune\Chatlog\Database\ChatlogUserRepo;

class CreateChatlogUsersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(
            ChatlogUserRepo::TABLE_NAME,
            function (Blueprint $table) {
                ChatlogUserRepo::createTable($table);
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(ChatlogUserRepo::TABLE_NAME);
    }
}
