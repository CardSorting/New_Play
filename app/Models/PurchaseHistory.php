<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'pack_id',
        'buyer_id', 
        'price',
        'purchased_at'
    ];

    protected $casts = [
        'purchased_at' => 'datetime',
    ];

    public function pack()
    {
        return $this->belongsTo(Pack::class);
    }

    public function buyer()
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
}
