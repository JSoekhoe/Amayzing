<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'phone', 'address', 'postcode', 'type', 'pickup_time', 'total_price', 'status',
    ];


    // Een order heeft meerdere producten (via order_items)
    // In Order.php
    public function items()
    {
        return $this->hasMany(\App\Models\OrderItem::class);
    }
}

