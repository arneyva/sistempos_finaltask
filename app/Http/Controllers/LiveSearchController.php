<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sale;
use Illuminate\Support\Facades\Auth;


class LiveSearchController extends Controller
{
    public function pos_sale_search(Request $request) {
        //ambil user akun ini 
        $user = Auth::user();
        //ambil warehousenya
        $warehouse = $user->warehouses->first();

        // Memecah kata kunci pencarian menjadi array
        $keywords = explode(' ', $request->input('q'));

        $results = Sale::where('warehouse_id', $warehouse->id)
                        ->where('is_pos', 1)
                        ->where(function ($query) use ($keywords) {
                            // Mencari berdasarkan setiap kata kunci
                            foreach ($keywords as $word) {
                                //menuju ke sale detail dan ke setiap produk
                                $query->orWhereHas('details.product', function ($subQuery) use ($word) {
                                    $subQuery->where('name', 'LIKE', '%' . $word . '%'); // mencari nama produk
                                })
                                        ->orWhereHas('details.product_variant', function ($subQuery2) use ($word) { // menuju ke produk varian dari produk
                                            $subQuery2->where('name', 'LIKE', '%' . $word . '%'); // mencari nama varian
                                        });
                                    // Mencari berdasarkan ref sale
                                $query->orWhere('Ref', 'LIKE', '%' . $word . '%');
                            }
                        })
                        ->get();

        return view('templates.cashier.create',compact('results'));
    }
}
