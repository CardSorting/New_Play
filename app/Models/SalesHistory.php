<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pack_id', 
        'transaction_id',
        'sale_amount',
        'sale_date',
        'status',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array',
        'sale_date' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function pack()
    {
        return $this->belongsTo(Pack::class);
    }

    public function transaction()
    {
        return $this->belongsTo(CreditTransaction::class);
    }
}
