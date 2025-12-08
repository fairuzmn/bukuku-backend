<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id', 'menu_item_id', 'quantity', 'price', 'subtotal', 'status'
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    // Relationship: The specific ticket for this item in the kitchen
    public function kitchenTicket(): HasOne
    {
        return $this->hasOne(KitchenTicket::class);
    }
}