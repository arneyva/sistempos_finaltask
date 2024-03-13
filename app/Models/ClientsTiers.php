<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClientsTiers extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'client_tiers';

    protected $fillable = [
        'id',
        'tier',
        'total_sales',
        'total_amount',
        'last_sale',
    ];
}
