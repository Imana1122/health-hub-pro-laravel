<?php

namespace App\Http\Controllers\dietician;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetToken;
use App\Models\Dietician;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class DieticianAuthController extends Controller
{

    public function processRegister(Request $request){
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|min:3',
            'last_name' => 'required',
            'email'=> 'required|email|unique:dieticians',
            'phone_number' => 'required|unique:dieticians',
            'cv' => 'required',
            'speciality' => 'required',
            'description' => 'required|max:255',
            'esewaId' => 'required',
            'booking_amount' => 'required|numeric',
            'bio' => 'required',
            'status' => 'required',

        ]);

        if ($validator->passes()){
            $dietician = Dietician::create([
                'name' => $request->name,
                'email'=> $request->email,
                'phone_number' => $request->phone_number,
                'speciality' => $request->speciality,
                'description' => $request->description,
                'esewaId' => $request->esewaId,
                'booking_amount'=> $request->booking_amount,
                'bio' => $request->bio,
                'status' => $request->status
                ]);
            if($dietician){
                $token = $dietician->createToken('auth_token')->accessToken;

                return response()->json([
                    'status'=> true,
                    'dietician' => $dietician,
                    'token' => $token,
                ]);

            }else{
                return response()->json([
                    'status' => false
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
        $dietician = Dietician::where('phone_number', $request->phone_number)->first();

        if (!$dietician) {
            return response()->json([
                'status' => false,
                'errors' => "Phone Number is incorrect."
            ]);
        } else {
            // Use Hash::check to compare the provided password with the hashed password in the database
            if (Hash::check($request->password, $dietician->password)) {
                $token = $dietician->createToken('auth_token')->accessToken;

                return response()->json([
                    'status' => true,
                    'dietician' => $dietician,
                    'token' => $token,
                ]);
            } else {
                return response()->json([
                    'status' => false,
                    'errors' => "Password incorrect."
                ]);
            }
        }
    } else {
        return response()->json([
            'status' => false,
            'errors' => $validator->errors()
        ]);
    }
}

    public function logout(Request $request){
        try {
            $request->user()->token()->revoke();
            return response()->json([
                'status' => true
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'error' => $e
            ]);
        }
    }


    public function changePassword(Request $request){

        $validator = Validator::make($request->all(), [
            'password'=> 'required|confirmed|min:5',
            'old_password'=> 'required',
        ]);

        if($validator->passes()){
            $dietician = Dietician::where('id',auth()->user()->id)->first();
            if(Hash::check($request->old_password,$dietician->password)){
                $dietician->password = Hash::make($request->password);
                $dietician->save();

                $message = 'Password updated successfully!';

                return response()->json([
                    'status'=> true,
                    'message'=> $message
                ]);
            }else{
                $message = 'Old password is incorrect. Please try again.';
                return response()->json([
                    'status'=> false,
                    'error'=> Hash::make($request->old_password)
                ]);
            }


        }else{
            $message = 'Old password is incorrect. Please try again.';
            return response()->json([
                'status'=> false,
                'errors'=> $validator->errors()
            ]);
        }
    }
    public function resetPasswordForm(Request $request){
        return view('reset-password');
    }

    public function sendcode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => [
                'required',
                'numeric',
                'digits:10',
                Rule::exists('dieticians')->where(function ($query) use ($request) {
                    // Include a where clause to check for the phone_number in the dieticians table.
                    $query->where('phone_number', $request->phone_number);
                }),
            ],
        ]);
        if ($validator->fails()) {

            return response()->json([
                'status'=> false,
                'errors'=> $validator->errors()
            ]);


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
                $queryParams = http_build_query(['phone_number' => $phone_number, 'code' => $code]);
                $resetRoute = url('reset-password') . '?' . $queryParams;
                $response = $client->post('https://sms.aakashsms.com/sms/v3/send', [
                    'form_params' => [
                        'auth_token' => 'c1eecbd817abc78626ee119a530b838ef57f8dad9872d092ab128776a00ed31d',
                        'to' => $phone_number,
                        'text' => "You can change your password here: $resetRoute",
                    ],
                ]);

                if ($response->getStatusCode() === 200) {
                    $message = 'Verification code sent successfully';
                    return response()->json([
                        'status'=> true,
                        'message'=> $message
                    ]);
                } else {
                    return response()->json([
                        'status'=> false,
                        'error'=> 'Failed to send verification code'
                    ]);
                }
            } catch (\Exception $e) {
                $message = 'Failed to send verification code. Check your Internet Connection.';
                return response()->json([
                    'status'=> false,
                    'errors'=> $message
                ]);
            }
        }


    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone_number' => ['required', 'numeric'],
            'code' => ['required', 'numeric'],
            'password'=> 'required|confirmed|min:5',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'=> false,
                'errors'=> $validator->errors()
            ]);

        }

        $phone_number = $request->phone_number;
        $code = $request->code;

        $verificationRecord = DB::table('password_reset_tokens')
            ->where('phone_number', $phone_number)
            ->latest()->first();

        if (!$verificationRecord) {
            $message =  'Verification code is missing.';
            return redirect()->back()->with('error', $message);

            // return response()->json([
            //     'status'=> false,
            //     'error'=> $message
            // ]);

        }else{
            $storedcode = $verificationRecord->code;

            if (Hash::check($code, $storedcode)) {
                if($verificationRecord->expires_at < now()){
                    $message =  'Verification code is expired.';
                    return redirect()->back()->with('error', $message);

                    // return response()->json([
                    //     'status'=> true,
                    //     'message'=> $message
                    // ]);
                }else{

                    $dietician = Dietician::where('phone_number', $request->phone_number)->first();
                    if (!$dietician) {
                        $message = 'Dietician with the given phone number not found! Try again';
                        return redirect()->back()->with('error', $message);

                        // return response()->json([
                        //     'status'=> false,
                        //     'message'=> $message
                        // ]);

                    }else{
                        $dietician->password = Hash::make($request->password);
                        $dietician->save();

                        $message ='Password successfully reset.';
                        return redirect()->back()->with('success', $message);

                        // return response()->json([
                        //     'status'=> true,
                        //     'message'=> $message
                        // ]);
                    }

                }

            } else {
                $message = 'Invalid verification code. Try regenerating again.';
                return redirect()->back()->with('error', $message);

                // return response()->json([
                //     'status'=> false,
                //     'error'=> $message
                // ]);

            }
        }


    }

    public function updateProfile(Request $request){
        $dieticianId = auth()->user()->id;

        if(!$dieticianId){
            return response()->json([
                'error'=> 'Dietician not found',
                'status'=>false
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'phone_number' => 'required|unique:dieticians,phone_number,' . $dieticianId . ',id',
            'email' => 'required|email|unique:dieticians,email,' . $dieticianId . ',id',

        ]);

        if($validator->passes()){

            $dietician = Dietician::find($dieticianId);
            $dietician->name = $request->name;
            $dietician->email = $request->email;
            $dietician->phone_number = $request->phone_number;
            $dietician->save();

            $message = 'Profile updated successfully';

            return response()->json([
                'status' => true,
                'message' => $message
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }


    }


}



