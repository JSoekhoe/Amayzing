<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'phone', 'street', 'postcode', 'housenumber', 'addition', 'type', 'pickup_time', 'total_price', 'status','payment_id', 'delivery_date', 'pickup_date','paid_at',
    ];


    // Een order heeft meerdere producten (via order_items)
    // In Order.php
    public function items()
    {
        return $this->hasMany(\App\Models\OrderItem::class);
    }
    public function getFullAddressAttribute()
    {
        return trim("{$this->street} {$this->housenumber} {$this->addition}, {$this->postcode}");
    }

}

