<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;
use Commune\Hyperf\Foundations\Database\TableSchema;

class CreateChatbotSessionDataTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(TableSchema::SESSION_DATA_TABLE, function (Blueprint $table) {
            $table->bigIncrements('id');
            TableSchema::id(TableSchema::SESSION_DATA_ID, $table);

            // 暂时无法控制 id 的长度. dueros message_id 超长, 导致了错误.
            TableSchema::id('message_id', $table,100);
            TableSchema::id('session_id', $table,100);
            TableSchema::id('chat_id', $table,100);
            TableSchema::id('user_id', $table,100);
            TableSchema::id('platform_id', $table, 100);
            TableSchema::id('chatbot_name', $table, 100);
            TableSchema::id('conversation_id', $table,100);

            TableSchema::serialized($table);
            $table->string(TableSchema::SESSION_DATA_TYPE, 100)->default('');

            $table->timestamps();

            $table->unique(TableSchema::SESSION_DATA_ID);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(TableSchema::SESSION_DATA_TABLE);
    }
}
