<?php

namespace App\Http\Controllers\Admin\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Inertia\Inertia;

class ConfirmGSMController extends Controller
{
    private function getMessageText($code)
    {
        return __('admin.confirm_gsm.smsContent', ['code' => $code]);
    }

    public function index()
    {
        $sessions['status'] = Session::get('status');
        $sessions['phones'] = Auth()->user()->adminPhones()->pluck('phone', 'id')->toArray();
        return Inertia::render('Admin/Auth/ConfirmGsm',compact('sessions'));
    }

    public function checkGSM()
    {
        return Inertia::render('Admin/Auth/CheckGsm');
    }

    public function validGSM(Request $request)
    {
        if (Auth::user()->login_code == $request->code && is_numeric($request->code)) {
            if (now() < Auth::user()->login_code_expired) {
                Session::put('admin_gsm_confirm', true);
                Auth::user()->update([
                    'login_code' => 'access',
                    'login_code_expired' => now()
                ]);
                return redirect()->route('admin.home');
            } else {
                $message = 'timeout';
            }
        } else {
            $message = 'wrong_code';
            Auth::user()->update([
                'login_code' => 'wrong_code',
                'login_code_expired' => now()
            ]);
        }

        Auth::guard('admin')->logout();
        return redirect()->route('admin.login')->with('code_rejected', __('user.auth.gsmValidError.' . $message));

    }

    public function confirm(Request $request)
    {
        $gsm = Auth::user()->adminPhones()->where('id', $request->number)->first();
        if (!empty($gsm)) {
            $gsm = $gsm->phone;
            $code = rand(100000, 999999);

            Auth::user()->update([
                'login_code' => $code,
                'login_code_expired' => now()->addMinutes(2)
            ]);


            $this->sendSMS($gsm, $this->getMessageText($code));

            return redirect()->route('admin.checkGsm');

        }

    }


    private function sendSMS($receiver, $message)
    {
        $postUrl = 'http://websms.telsam.com.tr/xmlapi/sendsms';
        $username = env('SMS_USERNAME');   //Panel girişi yaptığınız Kullanıcı Adınız
        $password = env('SMS_PASSWORD');    //Panel girişi yaptığınız Şifreniz
        $originator = env('SMS_TITLE'); //Buraya Başlık gireceksiniz

        $receiversStr = '<receiver>' . $receiver . '</receiver>';

        $xmlStr = '
<?xml version="1.0"?>
<SMS>
  <authentication>
    <username>' . $username . '</username>
    <password>' . $password . '</password>
  </authentication>
  <message>
    <originator>' . $originator . '</originator>
    <text>' . $message . '</text>
    <unicode></unicode>
  </message>
  <receivers>' . $receiversStr . '</receivers>
</SMS>';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $postUrl);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlStr);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $response = curl_exec($ch);

    }

}
