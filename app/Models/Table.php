<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Table extends Model
{
    protected $fillable = ['name', 'qr_code_path', 'status'];

    // Relationship: A table can have many orders over time
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
    
    // Helper to check if available
    public function isAvailable(): bool
    {
        return $this->status === 'available';
    }
}