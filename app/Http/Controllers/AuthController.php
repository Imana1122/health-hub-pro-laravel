<?php

namespace App\Http\Controllers;

use App\Mail\PasswordResetMail;
use App\Models\PasswordResetToken;
use App\Models\User;
use App\Models\UserProfile;
use App\Models\WeightPlan;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class AuthController extends Controller
{

    public function processRegister(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email'=> 'required|email|unique:users',
            'phone_number' => 'required|unique:users',
            'password' => 'required|confirmed',
        ]);

        if ($validator->passes()){
            $user = User::create([
                'name' => $request->name,
                'email'=> $request->email,
                'phone_number' => $request->phone_number,
                'password' => $request->password
                ]);
            if($request->isAttemptingPrecognition){
                $image = $request->image;
                $ext = $image->getClientOriginalExtension();
                $newName = $user->id.'.'.$ext;

                $user->image =$newName;


                $imagePath = $image->store('public/images');

                Storage::move($imagePath, 'public/uploads/users/' . $newName);



            }
            if($user){
                $token = $user->createToken('auth_token')->accessToken;

                return response()->json([
                    'status'=> true,
                    'user' => $user,
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
            "password" => "required",
        ]);

        if ($validator->passes()) {
            $user = User::where('phone_number', $request->phone_number)->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'error' => "Phone Number is incorrect."
                ]);
            } else {
                // Use Hash::check to compare the provided password with the hashed password in the database
                if (Hash::check($request->password, $user->password)) {
                    $token = $user->createToken('auth_token')->accessToken;
                    $userProfile = UserProfile::where('user_id',$user->id)->first();
                    $weightPlan=WeightPlan::where('id',$userProfile->weight_plan_id)->first();
                    $userProfile->weight_plan=$weightPlan->title ?? '';

                    $userCuisines = $user->cuisines()->get();
                    $userHealthConditions = $user->healthConditions()->get();
                    $userAllergens = $user->allergens()->get();


                    return response()->json([
                        'status' => true,
                        'user' => $user,
                        'token' => $token,
                        'userProfile'=>$userProfile,
                        'userCuisines' => $userCuisines,
                        'userHealthConditions' => $userHealthConditions,
                        'userAllergens' => $userAllergens,
                    ]);
                } else {
                    return response()->json([
                        'status' => false,
                        'error' => "Password incorrect."
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
            $user = User::where('id',auth()->user()->id)->first();
            if(Hash::check($request->old_password,$user->password)){
                $user->password = Hash::make($request->password);
                $user->save();

                $message = 'Password updated successfully!';

                return response()->json([
                    'status'=> true,
                    'message'=> $message
                ]);
            }else{
                $message = 'Old password is incorrect. Please try again.';
                return response()->json([
                    'status'=> false,
                    'error'=> $message
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
                Rule::exists('users')->where(function ($query) use ($request) {
                    // Include a where clause to check for the phone_number in the users table.
                    $query->where('phone_number', $request->phone_number);
                }),
            ],

        ]);
        $user=User::where('phone_number',$request->phone_number)->first();
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
                $queryParams = http_build_query(['phone_number' => $phone_number, 'code' => $code]);

                $resetRoute = url('reset-password') . '?' . $queryParams;
                Mail::to($user->email)->send(new PasswordResetMail(

                    "You can change your password here: $resetRoute"
                ));

                // $client = new Client();
                // $response = $client->post('https://sms.aakashsms.com/sms/v3/send', [
                //     'form_params' => [
                //         'auth_token' => 'c1eecbd817abc78626ee119a530b838ef57f8dad9872d092ab128776a00ed31d',
                //         'to' => $phone_number,
                //         'text' => "You can change your password here: $resetRoute",
                //     ],
                // ]);



                // if ($response->getStatusCode() === 200) {
                    $message = 'Password reset route sent to your phone number and email successfully';
                    return response()->json([
                        'status'=> true,
                        'message'=> $message
                    ]);
                // } else {
                //     return response()->json([
                //         'status'=> false,
                //         'message'=> 'Failed to send password reset route'
                //     ]);
                // }
            } catch (\Exception $e) {
                $message = 'Failed to send verification code. Check your Internet Connection.';
                return response()->json([
                    'status'=> false,
                    'message'=> $message
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



        }else{
            $storedcode = $verificationRecord->code;

            if (Hash::check($code, $storedcode)) {
                if($verificationRecord->expires_at < now()){
                    $message =  'Verification code is expired.';
                    return redirect()->back()->with('error', $message);


                }else{

                    $user = User::where('phone_number', $request->phone_number)->first();
                    if (!$user) {
                        $message = 'User with the given phone number not found! Try again';
                        return redirect()->back()->with('error', $message);



                    }else{
                        $user->password = Hash::make($request->password);
                        $user->save();

                        $message ='Password successfully reset.';
                        return redirect()->back()->with('success', $message);


                    }

                }

            } else {
                $message = 'Invalid verification code. Try regenerating again.';
                return redirect()->back()->with('error', $message);



            }
        }


    }

    public function updateInfo(Request $request){
        $userId = auth()->user()->id;

        if(!$userId){
            return response()->json([
                'error'=> 'User not found',
                'status'=>false
            ]);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'phone_number' => 'required|unique:users,phone_number,' . $userId . ',id',
            'email' => 'required|email|unique:users,email,' . $userId . ',id',

        ]);

        if($validator->passes()){

            $user = User::find($userId);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone_number = $request->phone_number;
            $user->save();

            $message = 'Profile updated successfully';

            return response()->json([
                'status' => true,
                'user' => $user
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }


    }

    public function completeProfile(Request $request){
        $userId = auth()->user()->id;

        if(!$userId){
            return response()->json([
                'error'=> 'User not found',
                'status'=>false
            ]);
        }

        $validator = Validator::make($request->all(), [
            'height' => 'required',
            'weight' => 'required',
            'waist' => 'required',
            'hips'=> "required",
            'targeted_weight' => "required",
            'age'=> 'required',
            'gender' => 'required',
            'calorie_difference'=>'required'
        ]);

        if($validator->passes()){

            $userProfile = UserProfile::updateOrCreate(
                ['user_id' => $userId],
                [
                    'height' => $request->height,
                    'weight' => $request->weight,
                    'waist' => $request->waist,
                    'hips' => $request->hips,
                    'bust' => $request->bust,
                    'targeted_weight' => $request->targeted_weight,
                    'gender' => $request->gender,
                    'age' => $request->age,
                    'calorie_difference'=>$request->calorie_difference
                ]
            );
            // Assuming $userProfile is an instance of UserProfile
            $weightPlan = $userProfile->weightPlan; // Correct

            $message = 'Profile completed successfully';

            return response()->json([
                'status' => true,
                'userProfile' => $userProfile,
                'weightPlan' => $weightPlan
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

    }


    public function updateProfileImage(Request $request){
        $id = auth()->user()->id;

        if(!$id){
            return response()->json([
                'error'=> 'User not found',
                'status'=>false
            ]);
        }

        $validator = Validator::make($request->all(), [
            'image'=>'required|image',

        ]);

        if($validator->passes()){

            $user = User::find($id);



            if($request->image){
                $image = $request->image;
                $ext = $image->getClientOriginalExtension();
                $newName = $user->id.'.'.$ext;

                $user->image =$newName;

                $imagePath = $image->store('public/uploads');

                Storage::move($imagePath, 'public/uploads/users/' . $newName);            }

            $user->save();

            $message = 'Profile Image updated successfully';

            return response()->json([
                'status' => true,
                'message' => $message,
                'data'=>$user
            ]);

        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }


    }



}



