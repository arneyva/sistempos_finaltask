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
 * Class Purchase
 *
 * @property int $id
 * @property int $user_id
 * @property string $Ref
 * @property Carbon $date
 * @property int $provider_id
 * @property int $warehouse_id
 * @property float|null $tax_rate
 * @property float|null $TaxNet
 * @property float|null $discount
 * @property float|null $shipping
 * @property float $GrandTotal
 * @property float $paid_amount
 * @property string $statut
 * @property string $payment_statut
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property Provider $provider
 * @property User $user
 * @property Warehouse $warehouse
 * @property Collection|PaymentPurchase[] $payment_purchases
 * @property Collection|PurchaseDetail[] $purchase_details
 * @property Collection|PurchaseReturn[] $purchase_returns
 */
class Purchase extends Model
{
    use SoftDeletes;

    protected $table = 'purchases';

    protected $casts = [
        'user_id' => 'int',
        'checked_by' => 'int',
        'date' => 'datetime',
        'req_arrive_date' => 'datetime',
        'estimate_arrive_date' => 'datetime',
        'provider_id' => 'int',
        'warehouse_id' => 'int',
        'down_payment' => 'int',
        'tax_rate' => 'float',
        'TaxNet' => 'float',
        'discount' => 'float',
        'shipping' => 'float',
        'GrandTotal' => 'float',
        'paid_amount' => 'float',
        'shipment_cost' => 'float'
    ];

    protected $fillable = [
        'user_id',
        'checked_by',
        'Ref',
        'date',
        'provider_id',
        'warehouse_id',
        'tax_rate',
        'TaxNet',
        'discount',
        'shipping',
        'GrandTotal',
        'paid_amount',
        'statut',
        'payment_statut',
        'notes',
        'payment_method',
        'payment_term',
        'courier',
        'down_payment',
        'supplier_notes',
        'req_arrive_date',
        'estimate_arrive_date',
        'shipment_number',
        'shipment_cost',
        'driver_contact',
        'barcode',
        'delivery_file'
    ];

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function checked_by()
    {
        return $this->belongsTo(User::class, 'checked_by');
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function payment_purchases()
    {
        return $this->hasMany(PaymentPurchase::class);
    }

    public function purchase_details()
    {
        return $this->hasMany(PurchaseDetail::class);
    }

    public function purchase_returns()
    {
        return $this->hasMany(PurchaseReturn::class);
    }
}
