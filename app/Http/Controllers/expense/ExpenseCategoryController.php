<?php

namespace App\Http\Controllers\expense;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
            'expense_category' => $expense_category,
            'all' => ExpenseCategory::all(),
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
            return back()->with('warning', 'Kategori tidak ditemukan!');
        }

        $rules = [
            'name' => 'required',
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

        session()->flash('success', 'Expense Category berhasil diedit');

        return response()->json(['message' => 'Expense Category berhasil diedit'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $expensecat = ExpenseCategory::findOrFail($id);
        if (! $expensecat) {
            return back()->with('warning', 'Kategori tidak ditemukan!');
        }

        $expensecat->delete();

        return back()->with('success', 'expensecat berhasil dihapus');
    }
}
