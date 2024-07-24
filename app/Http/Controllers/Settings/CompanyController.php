<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Hash;
use Intervention\Image\ImageManagerStatic as Image;

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
        $currentAvatar = $company->logo;
        if ($request->avatar != null) {

            $avatarBase64 = $request->input('avatar');

            $avatarBinaryData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $avatarBase64));
            $filename ='logo-default' . '.png';

            $tempFilePath = public_path('/hopeui/html/assets/images/avatars/temp/' . $filename);
            file_put_contents($tempFilePath, $avatarBinaryData);

            $image_resize = Image::make($tempFilePath);
            $image_resize->resize(128, 128);
            $image_resize->save(public_path('/hopeui/html/assets/images/avatars/' . $filename));
            unlink($tempFilePath);

            $path = public_path('/hopeui/html/assets/images/avatars/');
            $currentPhotoPath = $path . $currentAvatar;
            if (file_exists($currentPhotoPath)) {
                if ($currentAvatar != 'logo-default.png') {
                    @unlink($currentPhotoPath);
                }
            }
        } else {
            $filename = $currentAvatar;
        }
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
            'logo' => $filename,
        ]);
        return redirect()->route('settings.company.edit')->with('success', 'Company Profile updated successfully');
    }
}
