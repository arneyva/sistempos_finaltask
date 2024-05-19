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
 * Class ExpenseCategory
 * 
 * @property int $id
 * @property int $user_id
 * @property string $name
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * 
 * @property User $user
 * @property Collection|Expense[] $expenses
 *
 * @package App\Models
 */
class ExpenseCategory extends Model
{
	use SoftDeletes;
	protected $table = 'expense_categories';

	protected $casts = [
		'user_id' => 'int'
	];

	protected $fillable = [
		'user_id',
		'name',
		'description'
	];

	public function user()
	{
		return $this->belongsTo(User::class);
	}

	public function expenses()
	{
		return $this->hasMany(Expense::class);
	}
}
