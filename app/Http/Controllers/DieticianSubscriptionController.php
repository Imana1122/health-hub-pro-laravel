<?php

namespace App\Http\Controllers;

use App\Events\NotificationSent;
use App\Models\ChatUser;
use App\Models\Dietician;
use App\Models\DieticianBooking;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DieticianSubscriptionController extends Controller
{
    public function getDieticians(Request $request) {
        $userId = auth()->user()->id;

        $dieticianIdsWithRecentBookings = DieticianBooking::where('user_id', $userId)
            ->whereDate('created_at', '>', Carbon::now()->subDays(30))
            ->where('payment_status',1)
            ->pluck('dietician_id')
            ->toArray();


        $dieticians = Dietician::where('approved_status', 1)->with('ratings.user')->withCount('ratings')
        ->withSum('ratings','rating')
        ->whereNotIn('id', $dieticianIdsWithRecentBookings);
        if ($request->get('keyword') != '') {
            $dieticians = $dieticians->where('dieticians.first_name', 'like', '%' . $request->input('keyword') . '%')->orWhere('dieticians.last_name', 'like', '%' . $request->input('keyword') . '%')->orWhere('dieticians.email', 'like', '%' . $request->input('keyword') . '%');
        }
        $dieticians= $dieticians->paginate(4);
        foreach($dieticians as $dietician){
            //Rating Calculation
            $avgRating = '0.00';
            if($dietician->ratings_count > 0){
                $avgRating = number_format(($dietician->ratings_sum_rating/$dietician->ratings_count),2);

            }
            $dietician->avgRating = $avgRating;
        }



        return response()->json([
            'status' => true,
            'data' => $dieticians
        ]);
    }

    public function getBookedDieticians(Request $request) {
        $userId = auth()->user()->id;

        $dieticianIdsWithRecentBookings = DieticianBooking::where('user_id', $userId)
            ->whereDate('created_at', '>', Carbon::now()->subDays(30))
            ->where('payment_status',1)
            ->pluck('dietician_id')
            ->toArray();


        $dieticians = Dietician::where('approved_status', 1)->with('ratings.user')->withCount('ratings')
            ->withSum('ratings','rating')
            ->whereIn('id', $dieticianIdsWithRecentBookings);

        if ($request->get('keyword') != '') {
            $dieticians = $dieticians->where('dieticians.first_name', 'like', '%' . $request->input('keyword') . '%')->orWhere('dieticians.last_name', 'like', '%' . $request->input('keyword') . '%')->orWhere('dieticians.email', 'like', '%' . $request->input('keyword') . '%');
        }
        $dieticians= $dieticians->paginate(4);
        foreach($dieticians as $dietician){
            //Rating Calculation
            $avgRating = '0.00';
            if($dietician->ratings_count > 0){
                $avgRating = number_format(($dietician->ratings_sum_rating/$dietician->ratings_count),2);

            }
            $dietician->avgRating = $avgRating;
        }


        return response()->json([
            'status' => true,
            'data' => $dieticians
        ]);
    }

    public function bookDietician(Request $request){
        $validator = Validator::make($request->all(), [
            'dietician_id'=> 'required',
        ]);

        if ($validator->passes()){
            $dietician = Dietician::where('id',$request->dietician_id)->first();
            if($dietician){
                // Check if the authenticated user has any dieticianBookings with the dietician
                $user = User::where('id',auth()->user()->id)->first();
                $booking = $user->dieticianBookings()
                ->where('dietician_id', $dietician->id)
                ->latest('created_at')
                ->where('payment_status',1)
                ->first();
                // Check if booking exists and was created less than 30 days ago
                if ($booking) {
                    if($booking->created_at->diffInDays(now()) < 30){
                        return response()->json([
                            'status' => false,
                            'message'=> 'Dietician already booked'
                        ]);
                    }else{
                        $booking = DieticianBooking::create([
                            'user_id'=> auth()->user()->id,
                            'dietician_id' => $request->dietician_id,
                            'payment_status' => 0,
                            'total_amount' => $dietician->booking_amount

                        ]);

                        $dieticianBooking = DieticianBooking::with('dietician')->find($booking->id);



                        return response()->json([
                            'status' => true,
                            'data'=> $dieticianBooking
                        ]);
                    }
                }else{
                    $booking = DieticianBooking::create([
                        'user_id'=> auth()->user()->id,
                        'dietician_id' => $request->dietician_id,
                        'payment_status' => 0,
                        'total_amount' => $dietician->booking_amount

                    ]);
                    $dieticianBooking = DieticianBooking::with('dietician')->find($booking->id);

                    return response()->json([
                        'status' => true,
                        'data'=> $dieticianBooking
                    ]);
                }
            }else{
                return response()->json([
                    'status' => false,
                    'message'=> 'Dietician not found.'
                ]);
            }
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    public function verifyBookingPayment(Request $request){
        $validator = Validator::make($request->all(), [
            'productId'=> 'required',
            'totalAmount' => 'required',
            'status'=>'required',
            'refId' => 'required',
            'date'=>'required'
        ]);

        if ($validator->passes()){
            $booking = DieticianBooking::where('id',$request->productId)->first();
            if($booking && $request->status == 'COMPLETE'){

                // Check if booking exists and was created less than 30 days ago
                if ($booking->total_amount == $request->totalAmount) {
                    $booking->payment_status = 1;
                    $booking->created_at = Carbon::now();
                    $booking->updated_at = Carbon::now();

                    $booking->save();
                    $dietician=Dietician::where('id',$booking->dietician_id)->first();

                    $notification = new Notification([
                        'image' => asset('storage/uploads/dietician/profile/' . $dietician->image),
                        'message' => $dietician->first_name .$dietician->last_name. " is booked.",
                    ]);

                    $user=User::where('id',auth()->user()->id)->first();
                    // Assuming $user is the User model instance and $dietician is the Dietician model instance
                    $notification->user()->associate($user);
                    $notification->save();
                    $notification = Notification::where('id',$notification->id)->first();
                    $notification->to = 'user';

                    event(new NotificationSent($notification));

                    $notification = new Notification([
                        'image' => asset('storage/uploads/users/' . $user->image),
                        'message' => $user->name . " booked you.",
                    ]);

                    // Assuming $user is the User model instance and $dietician is the Dietician model instance
                    $notification->user()->associate($dietician);
                    $notification->save();
                    $notification = Notification::where('id',$notification->id)->first();

                    event(new NotificationSent($notification));

                    return response()->json([
                        'status' => true,
                        'message'=>'Dietician  booked successfully'
                    ]);

                }else{
                    $booking->delete();

                    return response()->json([
                        'status' => false,
                        'message'=> "Payment not successful for booking"
                    ]);
                }
            }else{
                return response()->json([
                    'status' => false,
                    'message'=> 'Booking not found.'
                ]);
            }
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }
}
