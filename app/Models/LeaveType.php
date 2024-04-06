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
 * Class LeaveType
 * 
 * @property int $id
 * @property string $title
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Collection|Leaf[] $leaves
 *
 * @package App\Models
 */
class LeaveType extends Model
{
	use SoftDeletes;
	protected $table = 'leave_types';

	protected $fillable = [
		'title'
	];

	public function leaves()
	{
		return $this->hasMany(Leave::class);
	}
}
