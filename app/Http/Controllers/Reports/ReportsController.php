<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;

class ReportsController extends Controller
{
    public function payments()
    {
        return view('templates.reports.payments');
    }

    public function profitLoss()
    {
        return view('templates.reports.profit-loss');
    }

    public function quantityAlerts()
    {
        return view('templates.reports.quantity-alerts');
    }

    public function stock()
    {
        return view('templates.reports.stock');
    }

    public function stockDetail($id)
    {
        return view('templates.reports.stock-detail');
    }
}
