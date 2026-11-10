<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewsletterSubscriber extends Model
{
    protected $fillable = [
        'email',
        'is_subscribed',
        'unsubscribe_token',
        'consented_at',
        'unsubscribed_at',
        'name',
    ];

    protected function casts(): array
    {
        return [
            'is_subscribed' => 'boolean',
            'consented_at' => 'datetime',
            'unsubscribed_at' => 'datetime',
        ];
    }
}
