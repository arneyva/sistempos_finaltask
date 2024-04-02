<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdjustmentDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'adjustment_details';

    protected $fillable = [
        'id', 'product_id', 'adjustment_id', 'quantity', 'type', 'product_variant_id',
    ];

    public function adjustment()
    {
        return $this->belongsTo('App\Models\Adjustment');
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }

    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
