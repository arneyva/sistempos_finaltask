<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Membership
 * 
 * @property int $id
 * @property float $spend_every
 * @property float $score_to_email
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 *
 * @package App\Models
 */
class Membership extends Model
{
	protected $table = 'membership';

	protected $casts = [
		'spend_every' => 'float',
		'score_to_email' => 'float'
	];

	protected $fillable = [
		'spend_every',
		'score_to_email'
	];
}
