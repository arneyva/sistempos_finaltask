<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PaymentSale extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'payment_sales';

    protected $fillable = [
        'sale_id', 'date', 'montant', 'Ref', 'change', 'Reglement', 'user_id', 'notes',
    ];

    protected $casts = [
        'montant' => 'double',
        'change' => 'double',
        'sale_id' => 'integer',
        'user_id' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function sale()
    {
        return $this->belongsTo('App\Models\Sale');
    }
}
