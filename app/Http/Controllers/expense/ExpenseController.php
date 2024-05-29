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

class ExpenseController extends Controller
{

    public function index()
    {
        // Dapatkan id dari user yang terautentikasi
        $userId = Auth::id();
        
        // Dapatkan array dari warehouse_id yang terkait dengan user yang terautentikasi
        $warehouseIds = Auth::user()->warehouses->pluck('id');

        $orderBy = 'created_at';
        $order = 'desc';
    
        $show = request('show') ?? '10';

        // Dapatkan user yang sedang diautentikasi
        $user = Auth::user();

        // Dapatkan nama role yang dimiliki oleh user
        $roles = $user->getRoleNames(); // Mengembalikan koleksi

        // Jika Anda hanya ingin menampilkan atau menggunakan role pertama
        $roleName = $roles->first();
        if($rolename='staff'||$rolename='inventaris')
        {
            $expenses= Expense::filter(['search'])->orderBy($orderBy, $order)->where('user_id', $userId)
            ->orWhereIn('warehouse_id', $warehouseIds)->paginate($show)->withQueryString();
        } else {
            $expenses= Expense::filter(['search'])->orderBy($orderBy, $order)->paginate($show)->withQueryString();
        }

        return view('templates.expense.index', [
            'expenses' => $expenses,
            'expense_category' => ExpenseCategory::all(),
            'Warehouses' => Warehouse::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Dapatkan array dari warehouse_id yang terkait dengan user yang terautentikasi
        $warehouseIds = Auth::user()->warehouses->pluck('id');

        return view('templates.expense.create', [
            'expense_category' => ExpenseCategory::all(),
            'warehouses' => Warehouse::where('id', $warehouseIds)->get(),
        ]);
    }

    public function store(Request $request)
    {
        
        // Dapatkan user yang sedang diautentikasi
        $user = Auth::user();

        // Dapatkan nama role yang dimiliki oleh user
        $roles = $user->getRoleNames(); // Mengembalikan koleksi

        // Jika Anda hanya ingin menampilkan atau menggunakan role pertama
        $roleName = $roles->first();
        if($rolename='staff'||$rolename='inventaris')
        {
            $rules = [
                'date' => 'required',
                'warehouse_id' => 'required',
                'category_id' => 'required',
                'details' => 'required',
                'amount' => 'required',
                'file_pendukung' => 'required',
            ];
    
            $message = [
                'required' => 'Tidak boleh kosong!',
            ];
        } else {
            $rules = [
                'date' => 'required',
                'warehouse_id' => 'required',
                'category_id' => 'required',
                'details' => 'required',
                'amount' => 'required',
                'file_pendukung' => 'required',
                'status' => 'required',
            ];
    
            $message = [
                'required' => 'Tidak boleh kosong!',
            ];
        }

        
        $validateData = $request->validate($rules, $message);

        $expense = new Expense;
        $expense->date = $request['date'];
        $expense->Ref = $this->getNumberOrder();
        $expense->user_id = Auth::user()->id;
        $expense->expense_category_id = $request['expense_category_id'];
        $expense->warehouse_id = $request['warehouse_id'];
        $expense->details = $request['details'];
        $expense->amount = $request['amount'];
        if($rolename='staff'||$rolename='inventaris')
        {
            $expense->status =  0;
        } else {
            if ($request['status'] === 1 || $request['status'] === 2 ){
                $expense->admin_id = Auth::user()->id;
                $expense->agreed_at= now();
            }
        }

        $expense->save();


        return redirect()->route('expenses.index')->with(['success' => 'Permintaan berhasil diproses']);
        
    }
    
    public function getNumberOrder()
    {
        $last = Expense::latest()->first();
        if ($last) {
            $item = $last->Ref;
            $nwMsg = explode("_", $item);
            $inMsg = $nwMsg[1] + 1;
            $code = $nwMsg[0] . '_' . $inMsg;
        } else {
            $code = 'EXP_0001';
        }
        return $code;
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('templates.expense.detail', [
            'expense' => Expense::findOrFail($id),
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        // Dapatkan array dari warehouse_id yang terkait dengan user yang terautentikasi
        $warehouseIds = Auth::user()->warehouses->pluck('id');

        return view('templates.expense.create', [
            'expense' => Expense::findOrFail($id),
            'expense_category' => ExpenseCategory::all(),
            'warehouses' => Warehouse::where('id', $warehouseIds)->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function updateAdmin(Request $request, string $id)
    {
        $expense = Expense::findOrFail($id);
        if (! $expense) {
            return back()->with('warning', 'Data tidak ditemukan');
        }

        // Dapatkan user yang sedang diautentikasi
        $user = Auth::user();

        // Dapatkan nama role yang dimiliki oleh user
        $roles = $user->getRoleNames(); // Mengembalikan koleksi

        // Jika Anda hanya ingin menampilkan atau menggunakan role pertama
        $roleName = $roles->first();
            $rules = [
                'date' => 'required',
                'warehouse_id' => 'required',
                'category_id' => 'required',
                'details' => 'required',
                'amount' => 'required',
                'file_pendukung' => 'required',
                'status' => 'required',
            ];
    
            $message = [
                'required' => 'Tidak boleh kosong!',
            ];


        $validateData = $request->validate($rules, $message);

        $expense->update([
            'date' => $request['date'],
            'user_id' => Auth::user()->id,
            'expense_category_id' => $request['expense_category_id'],
            'warehouse_id' => $request['warehouse_id'],
            'details' => $request['details'],
            'amount' => $request['amount'],
            'file_pendukung' => $request['file_pendukung'],
        ]);

        if($rolename='staff'||$rolename='inventaris')
        {
            if ($request['status'] == 0 || $request['status'] == 2)
            $expense->update([
                'status' => $request['status'],
            ]);
        } else {
            $expense->update([
                'status' => $request['status'],
            ]);
        }
        return redirect()->back()->with(['success' => 'Permintaan berhasil diproses']);
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $expense = Expense::findOrFail($id);
        if (! $expense) {
            return back()->with('warning', 'Data tidak ditemukan');
        }
        
        $expense->delete();
        return redirect()->back()->with(['success' => 'Permintaan berhasil diproses']);
    }
}
