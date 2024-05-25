<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use Illuminate\Http\Request;

class MembershipController extends Controller
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
        return view('templates.settings.membership.index', [
            'membership' => Membership::latest()->first(),
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
            'spend_every' => 'required|max:12',
            'one_score_equal' => 'required|email|unique:clients',
            'score_to_email' => 'required|numeric|min_digits:12|max_digits:12',
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
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
