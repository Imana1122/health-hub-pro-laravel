<?php

namespace App\Http\Controllers;


use App\Models\PasswordResetToken;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;


class AuthController extends Controller
{

    public function processRegister(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'phone_number' => 'required|unique:users',
        ]);
        $password = Str::random(8);

        if ($validator->passes()){
            $user = User::create([
                'name' => $request->name,
                'phone_number' => $request->phone_number,
                'password' => $password
                ]);
            if($user){
                try {

                    $client = new Client();
                    $response = $client->post('https://sms.aakashsms.com/sms/v3/send', [
                        'form_params' => [
                            'auth_token' => 'c1eecbd817abc78626ee119a530b838ef57f8dad9872d092ab128776a00ed31d',
                            'to' => $user->phone_number,
                            'text' => "You have successfully registered in Look Me Cosmetics. Your password is: $password"
                        ],
                    ]);

                    if ($response->getStatusCode() === 200) {
                        Auth::login($user);
                        $user = Auth::user();
                        // $token = $user->createToken('auth_token')->plainTextToken;


                        $message = 'You have registered successfully. Your auto-generated password is sent to your phone number. You can reset it.';

                        return response()->json([
                            'status'=> true,
                            'user' => $user,
                            // 'token' => $token,
                        ]);

                    } else {
                        $message = 'Failed to register.';
                        User::where('id', $user->id)->first()->delete();
                        return response()->json(['error' => $message, 'status' => false]);
                    }
                } catch (\Exception $e) {
                    $message = 'Failed to send SMS. Check your Internet Connection.';
                    User::where('id', $user->id)->first()->delete();
                    return response()->json(['error' => 'Failed to send SMS: ' . $e->getMessage(), 'message' => $message]);
                }
            }else{
                return response()->json([
                    'status'=> false,
                    'errors'=> 'Some errors in registering'
                ]);
            }


        }else{
            return response()->json([
                'status'=> 'false',
                'errors'=> $validator->errors()
            ]);
        }
    }

    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "phone_number" => "required|string",
            "password" => "required"
        ]);

        if ($validator->passes()) {
            if (Auth::attempt([
                'phone_number' => $request->phone_number,
                'password' => $request->password
            ], $request->get('remember'))) {
                $user = Auth::user();
                // $token = $user->createToken('auth_token')->plainTextToken;

                return response()->json(['user' => $user,
                //  'token' => $token,
                 'status'=>true]);

            } else {
                return response()->json([
                    'status'=> false,
                    'errors'=> 'Your phone number or password is incorrect.'
                ]);
            }
        } else {
            return response()->json([
                'status'=> false,
                'errors'=> $validator->errors()
            ]);
        }
    }



    public function logout(){
        Auth::logout();
        return redirect()->route('account.login')
        ->with('success', 'You successfully logged out.');
    }



    public function showChangePasswordForm(){
        return view('front.account.change-password');
    }

    public function changePassword(Request $request){

        $validator = Validator::make($request->all(), [
            'password'=> 'required|confirmed|min:5',
            'old_password'=> 'required',
        ]);

        if($validator->passes()){
            $user = User::select('id','password')->where('id',Auth::user()->id)->first();
            if(Hash::check($request->old_password,$user->password)){
                $user = User::where('id',$user->id)->update([
                    'password' => bcrypt($request->password),
                ]);

                $message = 'Password updated successfully!';
                session()->flash('success', $message);

                return response()->json([
                    'status'=> true,
                    'message'=> $message
                ]);
            }else{
                $message = 'Old password is incorrect. Please try again.';
                session()->flash('error', $message);
                return response()->json([
                    'status'=> true,
                    'error'=> $message
                ]);
            }


        }else{
            return redirect()->route('front.forgotPassword')->withInput()->withErrors($validator);
        }
    }


    public function showForgotPasswordForm(){
        return view('front.account.forgot-password');
    }

    public function sendcode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => [
                'required',
                'numeric',
                'digits:10',
                Rule::exists('users')->where(function ($query) use ($request) {
                    // Include a where clause to check for the phone_number in the users table.
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
                    return redirect()->route('account.showVerificationCodeForm')->with('phone_number', $phone_number);
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
        return view('front.account.verify-code');
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
                    return redirect()->route('account.showResetPasswordForm')->with('phone_number', $phone_number);

                }

            } else {
                $message = 'Invalid verification code. Try regenerating again.';
                return redirect()->back()->with('error', $message);

            }
        }


    }

    public function showResetPasswordForm(){
        return view('front.account.password-input');
    }

    public function resetPassword(Request $request){
        $validator = Validator::make($request->all(), [
            'password'=> 'required|confirmed|min:5',
            'phone_number' => 'required'
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->with('error','Failed validation. Try again.');
        }else{
            $user = User::where('phone_number', $request->phone_number)->first();
            if (!$user) {
                return redirect()->route('account.showForgotPasswordForm')->with('error','User with the given phone number not found! Try again');

            }else{
                $user->password = Hash::make($request->password);
                $user->save();
                if($user->role == 1){
                    return redirect()->route('account.login')->with('success','Password successfully reset.');

                }else{
                    return redirect()->route('admin.login')->with('success','Password successfully reset.');

                }
            }

        }
    }

}



