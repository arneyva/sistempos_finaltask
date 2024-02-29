<?php

namespace App\Models;

use App\Enums\WarehouseStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Warehouse extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'warehouses';

    protected $fillable = [
        'name',
        'city',
        'mobile',
        'zip',
        'email',
        'country',
        // 'status',
    ];

    // public function statusWarehouse(): Attribute
    // {
    //     return new Attribute(
    //         get: fn () => WarehouseStatus::getLabel($this->status),
    //     );
    // }

    public function scopeFilter(Builder $query, $filters = [])
    {
        if (isset($filters['q'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('name', 'LIKE', '%'.$filters['q'].'%')
                    ->orWhere('zip', 'LIKE', '%'.$filters['q'].'%')
                    ->orWhere('city', 'LIKE', '%'.$filters['q'].'%');
            });
        }

        return $query;
    }
}
