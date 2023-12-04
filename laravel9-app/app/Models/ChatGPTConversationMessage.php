<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatGPTConversationMessage extends Model
{
    use HasFactory;

    public function conversation() : BelongsTo
    {
        return $this->belongsTo(ChatGPTConversation::class, 'chatgpt_conversation_id');
    }
}
