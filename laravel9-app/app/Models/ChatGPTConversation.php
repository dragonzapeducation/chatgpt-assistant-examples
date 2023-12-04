<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatGPTConversation extends Model
{
    use HasFactory;

    public function messages()
    {
        return $this->hasMany(ChatGPTConversationMessage::class, 'chatgpt_conversation_id');
    }
}
