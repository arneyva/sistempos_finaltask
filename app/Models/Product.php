<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'products';

    protected $fillable = [
        'type',
        'code',
        'Type_barcode',
        'name',
        'cost',
        'price',
        'category_id',
        'brand_id',
        'unit_id',
        'unit_sale_id',
        'unit_purchase_id',
        'TaxNet',
        'tax_method',
        'image',
        'note',
        'stock_alert',
        'is_variant',
        'is_imei',
        'not_selling',
        'is_active',
    ];

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id', 'id');
    }
}
