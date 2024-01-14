<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Admin;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Models\PasswordResetToken;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;


class AdminLoginController extends Controller
{
    public function index()
    {
        return view("admin/login");
    }


    public function authenticate(Request $request){
       $validator = Validator::make($request->all(), [
        "phone_number"=> "required|string",
        "password"=> "required"
       ]);

        $admin = Admin::where('phone_number', $request->phone_number)->first();
        if($admin == null ){
            return redirect()->route('admin.login')->with('error','Phone number is incorrect');
        }

        if ($admin->status == 0) {
            return redirect()->back()
                ->with('error', 'Your account status is deactivated.');
        }


       if($validator->passes()){
            if(Auth::guard('admin')->attempt([
                'phone_number' => $request->phone_number,
                'password'=> $request->password]
                , $request->get('remember'))){
                    $admin = Auth::guard('admin')->user();


                    return redirect()->route('admin.dashboard');

            }else{
                return redirect()->route('admin.login')->with('error','Either phone number or password is incorrect');
            }
       }else{
        return redirect()->back()
            ->withErrors($validator)
            ->withInput($request->only('phone_number'));
       }
    }


    public function showForgotPasswordForm(){
        return view('admin.account.forgot-password');
    }

    public function sendcode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => [
                'required',
                'numeric',
                'digits:10',
                Rule::exists('admins')->where(function ($query) use ($request) {
                    // Include a where clause to check for the phone_number in the admins table.
                    $query->where('phone_number', $request->phone_number);
                }),
            ],
        ]);
        if ($validator->fails()) {

            return redirect()->back()
            ->with('error', 'Phone Number is incorrect')
            ->withErrors($validator)
            ->withInput($request->only('phone_number'));

        }else{

            $phone_number = strval($request->input('phone_number'));

            $code = mt_rand(10000, 99999);
            $encryptedCode= Hash::make($code);

            $phone_verification = PasswordResetToken::updateOrCreate(
                [
                    'phone_number'=> $phone_number,
                ],
                [
                'phone_number' => $phone_number,
                'code' => $encryptedCode,
                'expires_at' => now()->addMinutes(1),
                'status' => false,
                ]);
            try {
                $client = new Client();
                $response = $client->post('https://sms.aakashsms.com/sms/v3/send', [
                    'form_params' => [
                        'auth_token' => 'c1eecbd817abc78626ee119a530b838ef57f8dad9872d092ab128776a00ed31d',
                        'to' => $phone_number,
                        'text' => "Your verification code is: $code",
                    ],
                ]);

                if ($response->getStatusCode() === 200) {
                    $message = 'Verification code sent successfully';
                    return redirect()->route('admin.showVerificationCodeForm')->with('phone_number', $phone_number);
                } else {
                    return redirect()->back()
                    ->with('error', 'Failed to send verification code');
                }
            } catch (\Exception $e) {
                $message = 'Failed to send verification code. Check your Internet Connection.';
                return redirect()->back()->with('error', $message);
            }
        }


    }

    public function showVerificationCodeForm(){
        return view('admin.account.verify-code');
    }

    public function verifyCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => ['required', 'numeric'],
            'code' => ['required', 'numeric'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()
            ->with('error', 'Both code and phone_number are required')
            ->withErrors($validator)
            ->withInput($request->only('phone_number'));

        }

        $phone_number = $request->input('phone_number');
        $code = $request->input('code');

        $verificationRecord = DB::table('password_reset_tokens')
            ->where('phone_number', $phone_number)
            ->latest()->first();

        if (!$verificationRecord) {
            $message =  'Verification code is missing.';
            return redirect()->back()->with('error', $message);

        }else{
            $storedcode = $verificationRecord->code;

            if (Hash::check($code, $storedcode)) {
                if($verificationRecord->expires_at < now()){
                    $message =  'Verification code is expired.';
                    return redirect()->back()->with('error', $message);

                }else{

                    $message = 'Verification code is successful';
                    return redirect()->route('admin.showResetPasswordForm')->with('phone_number', $phone_number);

                }

            } else {
                $message = 'Invalid verification code. Try regenerating again.';
                return redirect()->back()->with('error', $message);

            }
        }


    }

    public function showResetPasswordForm(){
        return view('admin.account.password-input');
    }

    public function resetPassword(Request $request){
        $validator = Validator::make($request->all(), [
            'password'=> 'required|confirmed|min:5',
            'phone_number' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->with('error','Failed validation. Try again.');
        }else{
            $admin = Admin::where('phone_number', $request->phone_number)->first();
            if (!$admin) {
                return redirect()->route('account.showForgotPasswordForm')->with('error','Admin with the given phone number not found! Try again');

            }else{
                $admin->password = Hash::make($request->password);
                $admin->save();
                if($admin->role == 1){
                    return redirect()->route('admin.login')->with('success','Password successfully reset.');

                }else{
                    return redirect()->route('admin.login')->with('success','Password successfully reset.');

                }
            }

        }
    }


}
