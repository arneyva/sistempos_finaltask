<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotesTransfer extends Model
{
    use HasFactory;
    protected $table = 'notes_transfer';
    protected $fillable = ['transfer_id', 'user_id', 'note'];

    public function transfer()
    {
        return $this->belongsTo(Transfer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
