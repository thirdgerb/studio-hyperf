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
            TableSchema::scope($table);
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
