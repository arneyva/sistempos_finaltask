<?php

namespace App\Http\Controllers\expense;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\User;
use App\Models\Warehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        
        if ($roleName === 'staff' || $roleName === 'inventaris') {
            $expenses = Expense::filter(['search'])->orderBy($orderBy, $order)->where('user_id', $userId)
                ->orWhereIn('warehouse_id', $warehouseIds)->paginate($show)->withQueryString();
        } else {
            $expenses = Expense::filter(['search'])->orderBy($orderBy, $order)->paginate($show)->withQueryString();
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
            'warehouses' => Warehouse::whereIn('id', $warehouseIds)->get(),
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
        if ($roleName === 'staff' || $roleName === 'inventaris') {
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

        $file = $request->file('file_pendukung');
        $path = public_path().'/hopeui/html/assets/files/expenses';
        $filename = rand(11111111, 99999999).$file->getClientOriginalName();

        $file->move(public_path('/hopeui/html/assets/files/expenses/'), $filename);

        $jadikanFloat = floatval(str_replace(',', '.', str_replace('.', '', $request->input('amount'))));

        $expense = new Expense;
        $expense->date = $request->date;
        $expense->Ref = $this->getNumberOrder();
        $expense->user_id = Auth::user()->id;
        $expense->expense_category_id = $request->category_id;
        $expense->warehouse_id = $request->warehouse_id;
        $expense->details = $request->details;
        $expense->file_pendukung = $filename;
        $expense->amount = $jadikanFloat;
        if ($roleName === 'staff' || $roleName === 'inventaris') {
            $expense->status = 0;
        } else {
            $expense->status = $request->status;
            if ($request->status === '1' || $request->status === '2') {
                $expense->admin_id = Auth::user()->id;
                $expense->agreed_at = now();
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
            $nwMsg = explode('_', $item);
            $inMsg = $nwMsg[1] + 1;

            // Konversi variabel ke string untuk menghitung panjangnya
            $variabelString = (string) $inMsg;
            // Periksa jika panjang string kurang dari 4
            if (strlen($variabelString) < 4) {
                // Tambahkan nol di depan hingga panjangnya menjadi 4
                $variabelDiformat = str_pad($variabelString, 4, '0', STR_PAD_LEFT);
            } else {
                // Jika sudah 4 digit atau lebih, tidak perlu menambahkan nol
                $variabelDiformat = $variabelString;
            }

            $code = $nwMsg[0].'_'.$variabelDiformat;
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
        $expense = Expense::findOrFail($id);

        // Dapatkan array dari warehouse_id yang terkait dengan user yang terautentikasi
        $warehouseIds = Auth::user()->warehouses->pluck('id');

        // mengambil tanggal dari model
        $dateValue = ! empty($expense->date) ? $expense->date->format('Y-m-d') : old('date');

        return view('templates.expense.edit', [
            'expense' => $expense,
            'expense_category' => ExpenseCategory::all(),
            'warehouses' => Warehouse::whereIn('id', $warehouseIds)->get(),
            'dateValue' => $dateValue,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
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
            'status' => 'required',
        ];

        $message = [
            'required' => 'Tidak boleh kosong!',
        ];

        $validateData = $request->validate($rules, $message);

        $current = $expense->file_pendukung;
        if ($request->file_pendukung != null) {

            $file = $request->file('file_pendukung');
            $path = public_path().'/hopeui/html/assets/files/expenses/';
            $filename = rand(11111111, 99999999).$file->getClientOriginalName();

            $file->move(public_path('/hopeui/html/assets/files/expenses/'), $filename);

            $currentFile = $path.$current;
            if (file_exists($currentFile)) {
                @unlink($currentFile);
            }

        } else {
            $filename = $current;
        }

        $jadikanFloat = floatval(str_replace(',', '.', str_replace('.', '', $request->input('amount'))));

        $expense->update([
            'date' => $request->date,
            'expense_category_id' => $request->category_id,
            'warehouse_id' => $request->warehouse_id,
            'details' => $request->details,
            'amount' => $jadikanFloat,
            'file_pendukung' => $filename,
        ]);

        if ($roleName === 'staff' || $roleName === 'inventaris') {
            if ($expense->status == 0 && $request->status == '2') {
                $expense->update([
                    'status' => $request->status,
                    'admin_id' => Auth::user()->id,
                    'agreed_at' => now(),
                ]);
            }
        } elseif ($roleName === 'superadmin') {
            if ($expense->status === 0) {
                if ($request->status === '2' || $request->status === '1') {
                    $expense->update([
                        'admin_id' => Auth::user()->id,
                        'agreed_at' => now(),
                    ]);
                }
            } else {
                if ($request->status === '0') {
                    $expense->update([
                        'admin_id' => null,
                        'agreed_at' => null,
                    ]);
                } elseif ($request->status == $expense->status) {

                } else {
                    $expense->update([
                        'admin_id' => Auth::user()->id,
                        'agreed_at' => now(),
                    ]);
                }
            }
            $expense->update([
                'status' => $request->status,
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

    public function download($id)
    {
        $expense = Expense::find($id);
        $filePath = public_path('/hopeui/html/assets/files/expenses/'.$expense->file_pendukung);

        return response()->download($filePath);
    }
}
