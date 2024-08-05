<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transfer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id', 'date', 'user_id', 'from_warehouse_id', 'to_warehouse_id',
        'items', 'statut', 'notes', 'GrandTotal', 'discount', 'shipping', 'TaxNet', 'tax_rate',
        'created_at', 'updated_at', 'deleted_at',
    ];

    protected $table = 'transfers';

    // relasi ke detial
    public function details()
    {
        return $this->hasMany(TransferDetail::class, 'transfer_id', 'id');
    }

    // relasi ke warehouse asal
    public function from_warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'from_warehouse_id', 'id');
    }

    // relasi ke warehouse tujuan
    public function to_warehouse()
    {
        return $this->belongsTo(Warehouse::class, 'to_warehouse_id', 'id');
    }
    // app/Models/Transfer.php
    public function notes()
    {
        return $this->hasMany(NotesTransfer::class);
    }
}
