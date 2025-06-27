<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public function orderItems() {
        return $this->hasMany(OrderItem::class);
    }

    // in App\Models\Product.php
    protected $fillable = [
        'name',
        'description',
        'price',
        'pickup_stock',
        'delivery_stock',
        'is_active',
    ];


}
