<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatbotMessageHistory extends Model
{
    protected $fillable = [
        'user_id',
        'message',
        'sender', // 'user' ou 'bot'
        'sent_at',
    ];
}
