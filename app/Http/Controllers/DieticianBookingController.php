<?php

namespace App\Http\Controllers;

use App\Models\Dietician;
use App\Models\DieticianBooking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DieticianBookingController extends Controller
{
    public function getDieticians() {
        $userId = auth()->user()->id;

        $dieticianIdsWithRecentBookings = DieticianBooking::where('user_id', $userId)
        ->whereDate('created_at', '>', Carbon::now()->subDays(30))
        ->pluck('dietician_id')
        ->toArray();

    $dieticians = Dietician::where('approved_status', 1)
        ->whereNotIn('id', $dieticianIdsWithRecentBookings)
        ->paginate(4);

        return response()->json([
            'status' => true,
            'dieticians' => $dieticians
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
                ->first();
                // Check if booking exists and was created less than 30 days ago
                if ($booking) {
                    if($booking->created_at->diffInDays(now()) < 30){
                        return response()->json([
                            'status' => false,
                            'error'=> 'Dietician already booked'
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
                    'error'=> 'Dietician not found.'
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

                    // $dieticianBooking = DieticianBooking::with('dietician')->find($booking->id);

                    return response()->json([
                        'status' => true,
                        // 'data'=> $dieticianBooking
                    ]);

                }else{
                    $booking->delete();

                    return response()->json([
                        'status' => false,
                        'error'=> "Payment not successful for booking"
                    ]);
                }
            }else{
                return response()->json([
                    'status' => false,
                    'error'=> 'Booking not found.'
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
