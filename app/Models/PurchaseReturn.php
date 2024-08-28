<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PurchaseReturn
 * 
 * @property int $id
 * @property int $user_id
 * @property Carbon $date
 * @property string $Ref
 * @property int|null $purchase_id
 * @property int $provider_id
 * @property int $warehouse_id
 * @property float|null $tax_rate
 * @property float|null $TaxNet
 * @property float|null $discount
 * @property float|null $shipping
 * @property float $GrandTotal
 * @property float $paid_amount
 * @property string $payment_statut
 * @property string $statut
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Provider $provider
 * @property Purchase|null $purchase
 * @property User $user
 * @property Warehouse $warehouse
 * @property Collection|PaymentPurchaseReturn[] $payment_purchase_returns
 * @property Collection|PurchaseReturnDetail[] $purchase_return_details
 *
 * @package App\Models
 */
class PurchaseReturn extends Model
{
	use SoftDeletes;
	protected $table = 'purchase_returns';

	protected $casts = [
		'user_id' => 'int',
		'date' => 'datetime',
		'date' => 'datetime',
		'purchase_id' => 'int',
		'provider_id' => 'int',
		'warehouse_id' => 'int',
		'tax_rate' => 'float',
		'TaxNet' => 'float',
		'discount' => 'float',
		'shipping' => 'float',
		'GrandTotal' => 'float',
		'paid_amount' => 'float',
		'down_payment' => 'integer',
        'req_arrive_date' => 'date', // Akan dikonversi menjadi Carbon
        'estimate_arrive_date' => 'date', // Akan dikonversi menjadi Carbon
        'shipment_cost' => 'float',
        'request_req_arrive_date' => 'date', // Akan dikonversi menjadi Carbon
        'request_shipment_cost' => 'float',
        'subtotal' => 'float',
        'request_estimate_arrive_date' => 'date',
		'qty_unpassed_total' => 'float',
        'qty_return_total' => 'float',
        'qty_request_total' => 'float',
	];

	protected $fillable = [
		'user_id',
		'date',
		'Ref',
		'purchase_id',
		'provider_id',
		'warehouse_id',
		'tax_rate',
		'TaxNet',
		'discount',
		'shipping',
		'GrandTotal',
		'paid_amount',
		'payment_statut',
		'statut',
		'qty_unpassed_total',
		'qty_return_total',
		'qty_request_total',
		'notes',
		'courier',
		'address',
		'driver_contact',
		'shipment_number',
		'shipment_cost',
		'request_address',
		'request_req_arrive_date',
		'request_courier',
		'request_shipment_number',
		'request_driver_contact',
		'request_shipment_cost',
		'request_estimate_arrive_date',
		'request_delivery_file',
		'payment_method',
		'barcode',
		'supplier_ewalet',
		'retur_proof',
		'supplier_bank_account',
		'supplier_notes',
	];

	public function provider()
	{
		return $this->belongsTo(Provider::class);
	}

	public function purchase()
	{
		return $this->belongsTo(Purchase::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function warehouse()
	{
		return $this->belongsTo(Warehouse::class);
	}

	public function payment_purchase()
	{
		return $this->hasMany(PaymentPurchase::class);
	}

	public function purchase_return_details()
	{
		return $this->hasMany(PurchaseReturnDetail::class);
	}
}
