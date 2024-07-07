<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'currency_id', 'email', 'CompanyName', 'CompanyPhone', 'CompanyAdress', 'quotation_with_stock',
        'logo', 'footer', 'developed_by', 'client_id', 'warehouse_id', 'default_language',
        'is_invoice_footer', 'invoice_footer', 'server_password',
    ];

    protected $casts = [
        'currency_id' => 'integer',
        'client_id' => 'integer',
        'quotation_with_stock' => 'integer',
        'is_invoice_footer' => 'integer',
        'warehouse_id' => 'integer',
        'server_password' => 'hashed',
    ];

    protected $hidden = [
        'server_password',
    ];

    public function Currency()
    {
        return $this->belongsTo('App\Models\Currency');
    }

    public function Client()
    {
        return $this->belongsTo('App\Models\Client');
    }
}
