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
        $company = Setting::where('id', $id)->update([
            'CompanyName' => $updateRules['CompanyName'],
            'email' => $updateRules['email'],
            'CompanyPhone' => $updateRules['CompanyPhone'],
            'CompanyAdress' => $updateRules['CompanyAdress'],
        ]);
        return redirect()->route('settings.company.edit')->with('success', 'Company Profile updated successfully');
    }
}
