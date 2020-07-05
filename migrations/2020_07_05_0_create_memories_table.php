<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;
use Commune\Chatbot\Hyperf\Coms\Database\MemoryRepository;

class CreateMemoriesTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(
            MemoryRepository::TABLE_NAME,
            function (Blueprint $table) {
                MemoryRepository::createTable($table);
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(MemoryRepository::TABLE_NAME);
    }
}
