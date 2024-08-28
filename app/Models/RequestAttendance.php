<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class RequestAttendance
 * 
 * @property int $id
 * @property string|null $file_pendukung
 * @property string|null $details
 * @property Carbon|null $agreed_at
 * @property Carbon|null $date
 * @property int|null $status
 * @property int|null $user_id
 * @property int|null $admin_id
 * @property int|null $attendance_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Attendance|null $attendance
 * @property User|null $user
 *
 * @package App\Models
 */
class RequestAttendance extends Model
{
	use SoftDeletes;
	protected $table = 'request_attendances';

	protected $casts = [
		'agreed_at' => 'datetime',
		'date' => 'datetime',
		'status' => 'int',
		'user_id' => 'int',
		'admin_id' => 'int',
		'attendance_id' => 'int'
	];

	protected $fillable = [
		'file_pendukung',
		'details',
		'agreed_at',
		'date',
		'status',
		'user_id',
		'admin_id',
		'attendance_id'
	];

	public function attendance()
	{
		return $this->belongsTo(Attendance::class);
	}

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}

	public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
