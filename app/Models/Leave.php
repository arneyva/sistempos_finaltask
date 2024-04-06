<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Leave
 * 
 * @property int $id
 * @property int $leave_type_id
 * @property Carbon $start_date
 * @property Carbon $end_date
 * @property string $days
 * @property string|null $reason
 * @property string|null $attachment
 * @property bool|null $half_day
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int $user_id
 * 
 * @property LeaveType $leave_type
 * @property User $user
 *
 * @package App\Models
 */
class Leave extends Model
{
	use SoftDeletes;
	protected $table = 'leaves';

	protected $casts = [
		'leave_type_id' => 'int',
		'start_date' => 'datetime',
		'end_date' => 'datetime',
		'half_day' => 'bool',
		'user_id' => 'int'
	];

	protected $fillable = [
		'leave_type_id',
		'start_date',
		'end_date',
		'days',
		'reason',
		'attachment',
		'half_day',
		'status',
		'user_id'
	];

	public function leave_type()
	{
		return $this->belongsTo(LeaveType::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class);
	}
}
