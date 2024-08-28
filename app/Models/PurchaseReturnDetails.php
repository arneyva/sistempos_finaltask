<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PurchaseReturnDetail
 * 
 * @property int $id
 * @property float $cost
 * @property int|null $purchase_unit_id
 * @property float|null $TaxNet
 * @property string|null $tax_method
 * @property float|null $discount
 * @property string|null $discount_method
 * @property float $total
 * @property float $quantity
 * @property int $purchase_return_id
 * @property int $product_id
 * @property int|null $product_variant_id
 * @property string|null $imei_number
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Product $product
 * @property PurchaseReturn $purchase_return
 * @property ProductVariant|null $product_variant
 * @property Unit|null $unit
 *
 * @package App\Models
 */
class PurchaseReturnDetails extends Model
{
	use SoftDeletes;
	protected $table = 'purchase_return_details';

	protected $casts = [
		'cost' => 'float',
		'purchase_unit_id' => 'int',
		'TaxNet' => 'float',
		'discount' => 'float',
		'total' => 'float',
		'qty_unpassed' => 'float',
		'qty_return' => 'float',
		'qty_order' => 'float',
		'qty_request' => 'float',
		'purchase_return_id' => 'int',
		'product_id' => 'int',
		'product_variant_id' => 'int'
	];

	protected $fillable = [
		'cost',
		'purchase_unit_id',
		'TaxNet',
		'tax_method',
		'discount',
		'discount_method',
		'total',
		'qty_unpassed',
		'qty_return',
		'qty_order',
		'qty_request',
		'purchase_return_id',
		'product_id',
		'product_variant_id',
		'imei_number'
	];

	public function product()
	{
		return $this->belongsTo(Product::class);
	}

	public function purchase_return()
	{
		return $this->belongsTo(PurchaseReturn::class);
	}

	public function product_variant()
	{
		return $this->belongsTo(ProductVariant::class);
	}

	public function unit()
	{
		return $this->belongsTo(Unit::class, 'purchase_unit_id');
	}
}
