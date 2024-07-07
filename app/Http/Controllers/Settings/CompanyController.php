<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;

class CompanyController extends Controller
{
    public function edit(Request $request)
    {
        $company = Setting::where('id', '=', 1)->first();
        return view('templates.settings.company', ['company' => $company]);
    }
    public function update(Request $request, string $id)
    {
        $company = Setting::where('id', '=', 1)->first();
        $updateRules = $request->validate([
            'CompanyName' => [
                'required',
            ],
            'email' => [
                'required',
            ],
            'CompanyPhone' => [
                'required',
            ],
            'CompanyAdress' => ['required'],
        ]);

        $pass = $company->server_password;
        if ($request->server_password) {
            request()->validate([
                'server_password' => 'min:19'
            ]);

            $pass = Hash::make($request->server_password);
        }

        $company = Setting::where('id', $id)->update([
            'CompanyName' => $updateRules['CompanyName'],
            'email' => $updateRules['email'],
            'CompanyPhone' => $updateRules['CompanyPhone'],
            'CompanyAdress' => $updateRules['CompanyAdress'],
            'server_password' => $pass,
        ]);
        return redirect()->route('settings.company.edit')->with('success', 'Company Profile updated successfully');
    }
}
