<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
    public function update(Request $request, $id)
    {
        $membership = Membership::findOrFail($id);

        // Cek jenis form berdasarkan input 'action_type'
        switch ($request->input('action_type')) {
            case 'spend_every':
                // Handle form 1
                $jadikanFloat = floatval(str_replace(',', '.', str_replace('.', '', $request->input('spend_every'))));

                $dataToValidate = ['spend_every' => $jadikanFloat];

                $rules = ['spend_every' => ['required', 'numeric', 'gt:0']];
                $message = ['spend_every.gt' => 'Nilai harus lebih besar dari nol dan tidak boleh negatif.'];

                $validated = Validator::make($dataToValidate, $rules, $message);
                if ($validated->fails()) {
                    return back()->withErrors($validated)->withInput();
                }
                break;
            case 'one_score_equal':
                // Handle form 2
                $jadikanFloat = floatval(str_replace(',', '.', str_replace('.', '', $request->input('one_score_equal'))));

                $dataToValidate = ['one_score_equal' => $jadikanFloat];

                $rules = ['one_score_equal' => ['required', 'numeric', 'gt:0']];
                $message = ['one_score_equal.gt' => 'Nilai harus lebih besar dari nol dan tidak boleh negatif.'];

                $validated = Validator::make($dataToValidate, $rules, $message);
                if ($validated->fails()) {
                    return back()->withErrors($validated)->withInput();
                }
                break;
            case 'score_to_email':
                // Handle form 3
                $jadikanFloat = floatval(str_replace(',', '.', str_replace('.', '', $request->input('score_to_email'))));

                $dataToValidate = ['score_to_email' => $jadikanFloat];

                $rules = ['score_to_email' => ['required', 'numeric', 'gt:0']];
                $message = ['score_to_email.gt' => 'Nilai harus lebih besar dari nol dan tidak boleh negatif.'];

                $validated = Validator::make($dataToValidate, $rules, $message);
                if ($validated->fails()) {
                    return back()->withErrors($validated)->withInput();
                }
                break;
            default:
                break;
        }

        $membership->update($dataToValidate);

        // Redirect atau tampilkan view berdasarkan hasil handling
        return back()->with('success', 'Data Berhasil Diubah');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
