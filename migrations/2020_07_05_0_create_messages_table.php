<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;
use Commune\Chatbot\Hyperf\Coms\Database\MessageRepository;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(
            MessageRepository::TABLE_NAME,
            function (Blueprint $table) {
                MessageRepository::createTable($table);
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(MessageRepository::TABLE_NAME);
    }
}
