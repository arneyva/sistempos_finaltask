<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    public function ProductVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }

    public function PurchaseDetail()
    {
        return $this->hasMany('App\Models\PurchaseDetail');
    }

    public function SaleDetail()
    {
        return $this->belongsTo('App\Models\SaleDetail');
    }

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

    public function warehouse(): HasMany
    {
        return $this->hasMany(ProductWarehouse::class, 'product_id', 'id');
    }

    public function unitPurchase()
    {
        return $this->belongsTo('App\Models\Unit', 'unit_purchase_id');
    }

    public function unitSale()
    {
        return $this->belongsTo('App\Models\Unit', 'unit_sale_id');
    }
}
