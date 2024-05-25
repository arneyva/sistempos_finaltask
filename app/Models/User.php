<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasPermissions;
use Spatie\Permission\Traits\HasRoles;

/**
 * Class User
 *
 * @property int $id
 * @property string $firstname
 * @property string $lastname
 * @property string $username
 * @property string $email
 * @property string $password
 * @property string|null $avatar
 * @property string $phone
 * @property string|null $country
 * @property string|null $city
 * @property string|null $province
 * @property string|null $zipcode
 * @property string|null $address
 * @property string $gender
 * @property string|null $resume
 * @property string|null $document
 * @property Carbon|null $birth_date
 * @property Carbon|null $joining_date
 * @property int|null $remaining_leave
 * @property int|null $total_leave
 * @property float|null $hourly_rate
 * @property float|null $basic_salary
 * @property string|null $employment_type
 * @property string|null $marital_status
 * @property string|null $facebook
 * @property string|null $skype
 * @property string|null $whatsapp
 * @property string|null $twitter
 * @property string|null $linkedin
 * @property Carbon|null $leaving_date
 * @property bool $status
 * @property bool $is_all_warehouses
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $remember_token
 * @property Collection|Adjustment[] $adjustments
 * @property Collection|Attendance[] $attendances
 * @property Collection|ExpenseCategory[] $expense_categories
 * @property Collection|Expense[] $expenses
 * @property Collection|Leaf[] $leaves
 * @property Collection|OfficeShift[] $office_shifts
 * @property Collection|PaymentPurchaseReturn[] $payment_purchase_returns
 * @property Collection|PaymentPurchase[] $payment_purchases
 * @property Collection|PaymentSaleReturn[] $payment_sale_returns
 * @property Collection|PaymentSale[] $payment_sales
 * @property Collection|PurchaseReturn[] $purchase_returns
 * @property Collection|Purchase[] $purchases
 * @property Collection|Quotation[] $quotations
 * @property Collection|SaleReturn[] $sale_returns
 * @property Collection|Sale[] $sales
 * @property Collection|Shipment[] $shipments
 * @property Collection|Transfer[] $transfers
 * @property Collection|Warehouse[] $warehouses
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, HasPermissions, HasRoles, Notifiable, SoftDeletes;

    protected $table = 'users';

    protected $casts = [
        'birth_date' => 'datetime',
        'joining_date' => 'datetime',
        'remaining_leave' => 'int',
        'total_leave' => 'int',
        'hourly_rate' => 'float',
        'basic_salary' => 'float',
        'leaving_date' => 'datetime',
        'status' => 'integer',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $fillable = [
        'firstname',
        'lastname',
        'username',
        'email',
        'password',
        'avatar',
        'phone',
        'country',
        'city',
        'province',
        'zipcode',
        'address',
        'gender',
        'resume',
        'document',
        'birth_date',
        'joining_date',
        'remaining_leave',
        'total_leave',
        'hourly_rate',
        'basic_salary',
        'employment_type',
        'marital_status',
        'facebook',
        'skype',
        'whatsapp',
        'twitter',
        'linkedin',
        'leaving_date',
        'status',
        'remember_token',
    ];

    public function scopeFilter($query, array $filters)
    {

        $query->when($filters['search'] ?? false, function ($query, $search) {
            return $query
                ->where('firstname', 'like', '%'.$search.'%')
                ->orWhere('lastname', 'like', '%'.$search.'%')
                ->orWhere('email', 'like', '%'.$search.'%');
        });
    }

    public function adjustments()
    {
        return $this->hasMany(Adjustment::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function expense_categories()
    {
        return $this->hasMany(ExpenseCategory::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class);
    }

    public function office_shifts()
    {
        return $this->belongsToMany(OfficeShift::class)
            ->withPivot('id')
            ->withTimestamps();
    }

    public function payment_purchase_returns()
    {
        return $this->hasMany(PaymentPurchaseReturn::class);
    }

    public function payment_purchases()
    {
        return $this->hasMany(PaymentPurchase::class);
    }

    public function payment_sale_returns()
    {
        return $this->hasMany(PaymentSaleReturn::class);
    }

    public function payment_sales()
    {
        return $this->hasMany(PaymentSale::class);
    }

    public function purchase_returns()
    {
        return $this->hasMany(PurchaseReturn::class);
    }

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
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

    public function shipments()
    {
        return $this->hasMany(Shipment::class);
    }

    public function transfers()
    {
        return $this->hasMany(Transfer::class);
    }

    public function warehouses()
    {
        return $this->belongsToMany(Warehouse::class);
    }
}
