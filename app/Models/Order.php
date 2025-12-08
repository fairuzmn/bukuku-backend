<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = ['table_id', 'user_id', 'status', 'total_amount'];

    // Relationship: Order belongs to a Table
    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    // Relationship: Order belongs to a User (Waitstaff or Customer)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relationship: Order has many items
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
