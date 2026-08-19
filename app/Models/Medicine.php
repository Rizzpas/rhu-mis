<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Medicine extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'generic_name',
        'form',
        'category',
        'unit',
    ];

    public function batches()
    {
        return $this->hasMany(MedicineBatch::class);
    }

    public function inventoryLogs()
    {
        return $this->hasMany(InventoryLog::class);
    }

    public function getTotalStockAttribute()
    {
        return $this->batches()->whereDate('expiration_date', '>=', today())->sum('quantity');
    }
}
