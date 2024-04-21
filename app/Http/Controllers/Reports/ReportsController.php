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

    public function customers()
    {
        return view('templates.reports.customers');
    }

    public function customersDetail($id)
    {
        return view('templates.reports.customers-detail');
    }

    public function supplier()
    {
        return view('templates.reports.supplier');
    }

    public function supplierDetail($id)
    {
        return view('templates.reports.supplier-detail');
    }

    public function topSellingProduct()
    {
        return view('templates.reports.top-selling-product');
    }

    public function warehouse()
    {
        return view('templates.reports.warehouse');
    }

    public function sale()
    {
        return view('templates.reports.sale');
    }

    public function purchase()
    {
        return view('templates.reports.purchase');
    }
}
