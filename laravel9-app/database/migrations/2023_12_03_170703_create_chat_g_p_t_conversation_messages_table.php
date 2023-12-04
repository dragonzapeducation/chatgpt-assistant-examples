<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chat_g_p_t_conversation_messages', function (Blueprint $table) {
            $table->id();
            // Adding the foreign key reference to chat_g_p_t_conversations
            $table->foreignId('chatgpt_conversation_id')
                ->constrained('chat_g_p_t_conversations') // specify the table name
                ->onDelete('cascade'); // or choose the action you prefer

            $table->string('from');
            $table->longText('content');
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
        Schema::dropIfExists('chat_g_p_t_conversation_messages');
    }
};
