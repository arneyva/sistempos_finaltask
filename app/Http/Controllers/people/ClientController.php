<?php

namespace App\Http\Controllers\people;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;

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
            'name' => 'required|max:12',
            'email' => 'required|email|unique:clients',
            'phone' => 'required|numeric|min_digits:12|max_digits:12',
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
        $client->name = $request['name'];
        $client->email = $request['email'];
        $client->phone = $request['phone'];
        $client->save();

        return redirect()->route('people.clients.index')->with('success', 'Customer berhasil ditambahkan');
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
            'email' => 'required|email|unique:clients',
            'email' => Rule::unique('clients')->ignore($id),
            'phone' => 'required|numeric|min_digits:12|max_digits:12',
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

        Client::whereId($id)->update([
            'name' => $request['name'],
            'email' => $request['email'],
            'phone' => $request['phone'],
        ]);

        return back()->with('success', 'Client berhasil diedit');
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
