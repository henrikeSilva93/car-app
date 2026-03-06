<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatbotMessageHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'message',
        'sender',
        'sent_at',
    ];
}
