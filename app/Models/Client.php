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
 * Class Client
 * 
 * @property int $id
 * @property string|null $name
 * @property string $code
 * @property string|null $email
 * @property string $phone
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property float|null $score
 * @property int $client_tier_id
 * 
 * @property ClientTier $client_tier
 * @property Collection|Quotation[] $quotations
 * @property Collection|SaleReturn[] $sale_returns
 * @property Collection|Sale[] $sales
 * @property Collection|Setting[] $settings
 *
 * @package App\Models
 */
class Client extends Model
{
	use SoftDeletes;
	protected $table = 'clients';

	protected $casts = [
		'score' => 'float',
		'client_tier_id' => 'int'
	];

	protected $fillable = [
		'name',
		'code',
		'email',
		'phone',
		'score',
		'client_tier_id'
	];

	public function client_tier()
	{
		return $this->belongsTo(ClientTier::class);
	}

	public function quotations()
	{
		return $this->hasMany(Quotation::class);
	}

	public function sale_returns()
	{
		return $this->hasMany(SaleReturn::class);
	}

	public function sales()
	{
		return $this->hasMany(Sale::class);
	}

	public function settings()
	{
		return $this->hasMany(Setting::class);
	}
}
