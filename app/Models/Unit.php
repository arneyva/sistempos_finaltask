<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'units';

    protected $fillable = [
        'id',
        'name',
        'short_name',
        'base_unit_id',
        'operator',
        'operator_value',
        'description',
    ];

    // self-referencing atau self-join
    public function baseUnit()
    {
        return $this->belongsTo(Unit::class, 'base_unit_id')->onDelete('RESTRICT')->onUpdate('RESTRICT');
    }
}
