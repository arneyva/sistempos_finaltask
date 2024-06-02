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
 * Class OfficeShift
 * 
 * @property int $id
 * @property string $name
 * @property string|null $monday_in
 * @property string|null $monday_out
 * @property string|null $tuesday_in
 * @property string|null $tuesday_out
 * @property string|null $wednesday_in
 * @property string|null $wednesday_out
 * @property string|null $thursday_in
 * @property string|null $thursday_out
 * @property string|null $friday_in
 * @property string|null $friday_out
 * @property string|null $saturday_in
 * @property string|null $saturday_out
 * @property string|null $sunday_in
 * @property string|null $sunday_out
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property Collection|User[] $users
 * @property Collection|Warehouse[] $warehouses
 *
 * @package App\Models
 */
class OfficeShift extends Model
{
	use SoftDeletes;
	protected $table = 'office_shifts';

	protected $fillable = [
		'name',
		'monday_in',
		'monday_out',
		'tuesday_in',
		'tuesday_out',
		'wednesday_in',
		'wednesday_out',
		'thursday_in',
		'thursday_out',
		'friday_in',
		'friday_out',
		'saturday_in',
		'saturday_out',
		'sunday_in',
		'sunday_out'
	];

	public function users()
	{
		return $this->belongsToMany(User::class)
					->withPivot('id')
					->withTimestamps();
	}

	public function warehouses()
	{
		return $this->belongsToMany(Warehouse::class)
					->withPivot('id')
					->withTimestamps();
	}
}
