<?php

namespace App\Http\Controllers\expense;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Expense;
use App\Models\User;
use App\Models\ExpenseCategory;
use App\Models\Warehouse;

class ExpenseCategoryController extends Controller
{

    public function __construct()
    {
        $this->middleware('role:superadmin');
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orderBy = 'created_at';
        $order = 'desc';

        $show = request('show') ?? '10';
        $expense_category = ExpenseCategory::filter((['search']))->orderBy($orderBy, $order)->paginate($show)->withQueryString();

        return view('templates.expense.category.index', [
            'clients' => $clients,
            'allClients' => Client::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $rules = [
            'name_create' => 'required',
            'description_create' => 'required',
        ];
        $message = [
            'required' => 'Tidak boleh kosong!',
            'email' => 'Alamat email tidak valid!',
            'min' => 'Minimal :min karakter',
            'min_digits' => 'Nomor terdiri dari :min angka',
            'max' => 'Maksimal :max karakter',
            'max_digits' => 'Nomor terdiri dari :max angka',
            'unique' => ':attribute sudah terdaftar',
        ];


        $validateData = $request->validate($rules, $message);

        $expensecat = new ExpenseCategory;
        $expensecat->name = $request['name_create'];
        $expensecat->user_id = Auth::user()->id;
        $expensecat->description = $request['description_create'];
        $expensecat->save();

        return redirect()->route('expenses.categories.index')->with(['success' => 'Permintaan berhasil diproses']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $expensecat = ExpenseCategory::findOrFail($id);
        if (! $expensecat) {
            return back()->with('warning', 'Client tidak ditemukan!');
        }

        $rules = [
            'name' => 'required',
            'description' => 'required',
        ];
        $message = [
            'required' => 'Tidak boleh kosong!',
            'email' => 'Alamat email tidak valid!',
            'min' => 'Minimal :min karakter',
            'min_digits' => 'Nomor terdiri dari :min angka',
            'max' => 'Maksimal :max karakter',
            'max_digits' => 'Nomor terdiri dari :max angka',
            'unique' => ':attribute sudah terdaftar',
        ];

        $validateData = $request->validate($rules, $message);

        ExpenseCategory::whereId($id)->update([
            'name' => $request['name'],
            'description' => $request['description'],
        ]);

        return redirect()->route('expenses.categories.index')->with(['success' => 'Permintaan berhasil diproses']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $expensecat = ExpenseCategory::findOrFail($id);
        if (! $expensecat) {
            return back()->with('warning', 'Client tidak ditemukan!');
        }

        $expensecat->delete();

        return back()->with('success', 'expensecat berhasil dihapus');
    }
}
