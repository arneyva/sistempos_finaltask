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
}
