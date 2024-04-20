<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Attendance
 *
 * @property int $id
 * @property int $user_id
 * @property Carbon $date
 * @property string $clock_in
 * @property string $clock_in_ip
 * @property string $clock_out
 * @property string $clock_out_ip
 * @property bool $clock_in_out
 * @property string $depart_early
 * @property string $late_time
 * @property string $overtime
 * @property string $total_work
 * @property string $total_rest
 * @property string $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property User $user
 */
class Attendance extends Model
{
    use SoftDeletes;

    protected $table = 'attendances';

    protected $casts = [
        'user_id' => 'int',
        'date' => 'datetime',
        'clock_in_out' => 'bool',
    ];

    protected $fillable = [
        'user_id',
        'date',
        'clock_in',
        'clock_in_ip',
        'clock_out',
        'clock_out_ip',
        'clock_in_out',
        'depart_early',
        'late_time',
        'overtime',
        'total_work',
        'total_rest',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
