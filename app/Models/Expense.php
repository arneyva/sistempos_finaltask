<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Expense
 * 
 * @property int $id
 * @property Carbon $date
 * @property string $Ref
 * @property int $user_id
 * @property int $expense_category_id
 * @property int $warehouse_id
 * @property string $details
 * @property float $amount
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int|null $admin_id
 * 
 * @property User $user
 * @property ExpenseCategory $expense_category
 * @property Warehouse $warehouse
 *
 * @package App\Models
 */
class Expense extends Model
{
	use SoftDeletes;
	protected $table = 'expenses';

	protected $casts = [
		'date' => 'datetime',
		'user_id' => 'int',
		'expense_category_id' => 'int',
		'warehouse_id' => 'int',
		'amount' => 'float',
		'admin_id' => 'int'
	];

	protected $fillable = [
		'date',
		'Ref',
		'user_id',
		'expense_category_id',
		'warehouse_id',
		'details',
		'amount',
		'admin_id'
	];

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id');
	}

	public function admin()
	{
		return $this->belongsTo(User::class, 'admin_id');
	}

	public function expense_category()
	{
		return $this->belongsTo(ExpenseCategory::class);
	}

	public function warehouse()
	{
		return $this->belongsTo(Warehouse::class);
	}
}
