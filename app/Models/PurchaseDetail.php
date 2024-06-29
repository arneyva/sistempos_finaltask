<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PurchaseDetail
 * 
 * @property int $id
 * @property float $cost
 * @property int|null $purchase_unit_id
 * @property float|null $TaxNet
 * @property string|null $tax_method
 * @property float|null $discount
 * @property string|null $discount_method
 * @property int $purchase_id
 * @property int $product_id
 * @property int|null $product_variant_id
 * @property string|null $imei_number
 * @property float $total
 * @property float $quantity
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property float|null $quantity_recieved
 * @property string $status
 * 
 * @property Product $product
 * @property Purchase $purchase
 * @property ProductVariant|null $product_variant
 * @property Unit|null $unit
 *
 * @package App\Models
 */
class PurchaseDetail extends Model
{
	protected $table = 'purchase_details';

	protected $casts = [
		'cost' => 'float',
		'purchase_unit_id' => 'int',
		'TaxNet' => 'float',
		'discount' => 'float',
		'purchase_id' => 'int',
		'product_id' => 'int',
		'product_variant_id' => 'int',
		'total' => 'float',
		'quantity' => 'float',
		'quantity_recieved' => 'float'
	];

	protected $fillable = [
		'cost',
		'purchase_unit_id',
		'TaxNet',
		'tax_method',
		'discount',
		'discount_method',
		'purchase_id',
		'product_id',
		'product_variant_id',
		'imei_number',
		'total',
		'quantity',
		'quantity_recieved',
		'status'
	];

	public function product()
	{
		return $this->belongsTo(Product::class);
	}

	public function purchase()
	{
		return $this->belongsTo(Purchase::class);
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
