<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Warehouse
 *
 * @property int $id
 * @property string $name
 * @property string|null $city
 * @property string|null $mobile
 * @property string|null $zip
 * @property string|null $email
 * @property string|null $country
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property Collection|Adjustment[] $adjustments
 * @property Collection|Expense[] $expenses
 * @property Collection|Product[] $products
 * @property Collection|PurchaseReturn[] $purchase_returns
 * @property Collection|Purchase[] $purchases
 * @property Collection|Quotation[] $quotations
 * @property Collection|SaleReturn[] $sale_returns
 * @property Collection|Sale[] $sales
 * @property Collection|Setting[] $settings
 * @property Collection|Transfer[] $transfers
 * @property Collection|User[] $users
 */
class Warehouse extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'warehouses';

    protected $fillable = [
        'name',
        'city',
        'mobile',
        'zip',
        'email',
        'country',
    ];

    public function adjustments()
    {
        return $this->hasMany(Adjustment::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('id', 'product_variant_id', 'manage_stock', 'stock_alert', 'qty', 'deleted_at')
            ->withTimestamps();
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

    public function settings()
    {
        return $this->hasMany(Setting::class);
    }

    public function transfers()
    {
        return $this->hasMany(Transfer::class, 'to_warehouse_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
