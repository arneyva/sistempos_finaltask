<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferDetail extends Model
{
    use HasFactory;

    protected $table = 'transfer_details';

    protected $fillable = [
        'id', 'transfer_id', 'quantity', 'purchase_unit_id', 'product_id', 'total', 'product_variant_id',
        'cost', 'TaxNet', 'discount', 'discount_method', 'tax_method',
    ];

    public function transfer()
    {
        return $this->belongsTo('App\Models\Transfer');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }
}
