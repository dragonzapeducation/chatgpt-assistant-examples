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
        Schema::create('chat_g_p_t_conversations', function (Blueprint $table) {
            $table->id();
            //This is the save state of a conversation, found by calling Conversation::getIdentificationData()->getSaveDataString();
            $table->string('saved_state', 170)->nullable();
            // The thread ID from the OpenAI API
            $table->string('thread_id', 100);

            $table->string('assistant_codename', 120);
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
        Schema::dropIfExists('chat_g_p_t_conversations');
    }
};
