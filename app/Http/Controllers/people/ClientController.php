<?php

namespace App\Http\Controllers\people;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function __construct()
    {
        $this->middleware('role:superadmin|staff|inventaris');
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orderBy = 'created_at';
        $order = 'desc';

        $show = request('show') ?? '10';
        $clients = Client::filter((['search']))->orderBy($orderBy, $order)->paginate($show)->withQueryString();

        return view('templates.people.customer.index', [
            'clients' => $clients,
            'allClients' => Client::all(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $rules = [
            'name_create' => 'required',
            'email_create' => 'required|email|unique:clients,email',
            'phone_create' => 'required|numeric|min_digits:12|max_digits:12',
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

        $client = new Client;
        $client->name = $request['name_create'];
        $client->email = $request['email_create'];
        $client->phone = $request['phone_create'];
        $client->save();

        return redirect()->route('people.clients.index')->with(['success' => 'Client berhasil ditambahkan']);
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
        $client = Client::findOrFail($id);
        if (! $client) {
            return back()->with('warning', 'Client tidak ditemukan!');
        }

        $rules = [
            'name' => 'required|max:12',
            'email' => ['required', 'email', Rule::unique('clients')->ignore($id)],
            'phone' => 'required|numeric|digits:12',
        ];

        $messages = [
            'required' => 'Tidak boleh kosong!',
            'email' => 'Alamat email tidak valid!',
            'digits' => 'Nomor telepon harus terdiri dari :digits digit.',
            'unique' => ':attribute sudah terdaftar',
        ];

        $validateData = $request->validate($rules, $messages);

        $client->update($validateData);

        session()->flash('success', 'Client berhasil diedit');

        return response()->json(['message' => 'Client berhasil diedit'], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $client = Client::findOrFail($id);
        if (! $client) {
            return back()->with('warning', 'Client tidak ditemukan!');
        }

        $client->delete();

        return back()->with('success', 'Client berhasil dihapus');
    }
}
