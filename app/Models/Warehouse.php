<?php

namespace App\Models;

use App\Enums\WarehouseStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'warehouses';
    protected $fillable = [
        'name',
        'city',
        'telephone',
        'postcode',
        'email',
        'country',
        'status',
    ];
    public function statusWarehouse(): Attribute
    {
        return new Attribute(
            get: fn () => WarehouseStatus::getLabel($this->status),
        );
    }
}
