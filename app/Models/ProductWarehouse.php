<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductWarehouse extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'product_warehouse';

    protected $fillable = [
        'product_id', 'warehouse_id', 'qte', 'manage_stock',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id', 'id');
    }

    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class, 'product_variant_id', 'id');
    }
}
