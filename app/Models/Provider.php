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
 * Class Provider
 * 
 * @property int $id
 * @property string $name
 * @property int $code
 * @property string|null $email
 * @property string|null $phone
 * @property string|null $tax_number
 * @property string|null $country
 * @property string|null $city
 * @property string|null $adresse
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Collection|PurchaseReturn[] $purchase_returns
 * @property Collection|Purchase[] $purchases
 *
 * @package App\Models
 */
class Provider extends Model
{
	use SoftDeletes;
	protected $table = 'providers';

	protected $casts = [
		'code' => 'int'
	];

	protected $fillable = [
		'name',
		'code',
		'email',
		'phone',
		'tax_number',
		'country',
		'city',
		'adresse'
	];

	public function purchase_returns()
	{
		return $this->hasMany(PurchaseReturn::class);
	}

	public function purchases()
	{
		return $this->hasMany(Purchase::class);
	}
}
