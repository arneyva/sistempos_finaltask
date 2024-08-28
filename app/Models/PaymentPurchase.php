<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class PaymentPurchase
 * 
 * @property int $id
 * @property int $user_id
 * @property Carbon $date
 * @property string $Ref
 * @property int $purchase_id
 * @property float $montant
 * @property float $change
 * @property string $Reglement
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Purchase $purchase
 * @property User $user
 *
 * @package App\Models
 */
class PaymentPurchase extends Model
{
	use SoftDeletes;
	protected $table = 'payment_purchases';

	protected $casts = [
		'user_id' => 'int',
		'date' => 'datetime',
		'purchase_id' => 'int',
		'purchase_return_id' => 'int',
		'montant' => 'float',
		'change' => 'float'
	];

	protected $fillable = [
		'user_id',
		'date',
		'Ref',
		'purchase_id',
		'purchase_return_id',
		'montant',
		'change',
		'Reglement',
		'payment_proof',
		'notes'
	];

	public function purchase()
	{
		return $this->belongsTo(Purchase::class);
	}
	public function purchase_return()
	{
		return $this->belongsTo(PurchaseReturn::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
