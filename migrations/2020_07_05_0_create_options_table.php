<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;
use Commune\Chatbot\Hyperf\Coms\Database\OptionRepository;

class CreateOptionsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(
            OptionRepository::TABLE_NAME,
            function (Blueprint $table) {
                OptionRepository::createTable($table);
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(OptionRepository::TABLE_NAME);
    }
}
