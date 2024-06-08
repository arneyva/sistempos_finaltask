<?php

namespace App\Http\Controllers\people;

use App\Http\Controllers\Controller;
use App\Models\Provider;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProviderController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:superadmin|inventaris');
    }

    public function index()
    {
        $orderBy = 'name';
        $order = 'asc';

        $show = request('show') ?? '10';
        $providers = Provider::filter((['search']))->orderBy($orderBy, $order)->paginate($show)->withQueryString();

        return view('templates.people.supplier.index', [
            'providers' => $providers,
            'allProviders' => Provider::all(),
        ]);
    }

    public function create()
    {

        return view('templates.people.supplier.create', [
            'provider' => Provider::all(),
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:providers',
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

        $last = Provider::latest()->first();
        if ($last) {
            $item = $last->code;
            $code = $item + 1;
        } else {
            $code = 1111;
        }

        $provider = new Provider;
        $provider->name = $request['name'];
        $provider->email = $request['email'];
        $provider->phone = $request['phone'];
        $provider->country = $request['country'];
        $provider->code = $code;
        $provider->city = $request['city'];
        $provider->adresse = $request['adresse'];
        $provider->nama_kontak_person = $request['nama_kontak_person'];
        $provider->alamat_website = $request['alamat_website'];
        $provider->lead_time = $request['lead_time'];
        $provider->nomor_kontak_person = $request['nomor_kontak_person'];
        $provider->save();

        return redirect()->route('people.suppliers.index', ['orderBy' => 'name'])->with('success', 'Supplier berhasil ditambahkan');
    }

    public function show(string $id)
    {
        return view('templates.people.supplier.edit', [
            'provider' => Provider::findOrFail($id),
        ]);
    }

    public function update(Request $request, string $id)
    {
        $provider = Provider::findOrFail($id);
        if (! $provider) {
            return back()->with('warning', 'Supplier tidak ditemukan!');
        }

        $rules = [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:providers',
            'email' => Rule::unique('providers')->ignore($id),
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

        Provider::whereId($id)->update([
            'name' => $request['name'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'country' => $request['country'],
            'city' => $request['city'],
            'adresse' => $request['adresse'],
            'nama_kontak_person' => $request['nama_kontak_person'],
            'alamat_website' => $request['alamat_website'],
            'lead_time' => $request['lead_time'],
            'nomor_kontak_person' => $request['nomor_kontak_person'],
        ]);

        return back()->with('success', 'Supplier berhasil diedit');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $provider = Provider::findOrFail($id);
        if (! $provider) {
            return back()->with('warning', 'Supplier tidak ditemukan!');
        }

        $provider->delete();

        return back()->with('success', 'Supplier berhasil dihapus');
    }
}
