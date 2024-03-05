<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'units';

    protected $fillable = [
        'id',
        'name',
        'ShortName',
        'base_unit',
        'operator',
        'operator_value',
    ];

    // self-referencing atau self-join
    public function baseUnit()
    {
        return $this->belongsTo(Unit::class, 'base_unit');  /*->onDelete('RESTRICT')->onUpdate('RESTRICT') taruh di migartion */
    }
}
