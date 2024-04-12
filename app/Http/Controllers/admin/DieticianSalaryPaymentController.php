<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Dietician;
use App\Models\DieticianBooking;
use App\Models\DieticianSalaryPayment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DieticianSalaryPaymentController extends Controller
{
    public function index(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'year' => 'required|numeric',
            'month' => 'required|numeric',
        ]);

        if($validator->passes()){
            $year = $request->year;
            $month = $request->month;
        }else{
            $year = now()->year;
            $month = now()->month;
        }

        $dietician = Dietician::findOrFail($id);
        $dieticianPayment = DieticianSalaryPayment::where('dietician_id',$id)->where('year',$year)->where('month',$month)->first();
        // Get the start date of the month
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();

        // Get the end date of the month
        $endDate = $startDate->copy()->endOfMonth();

        $dieticianBookings = DieticianBooking::with('user')->where('dietician_id', $dietician->id)
            ->where('payment_status', 1)
            ->where(function ($query) use ($startDate, $endDate,$year,$month) {
                $query->where(function ($query) use ($startDate, $endDate) {
                    // Subscriptions starting in the specified month
                    $query->whereDate('updated_at', '>=', $startDate)
                        ->whereDate('updated_at', '<=', $endDate);
                })->orWhere(function ($query) use ($startDate, $year,$month) {
                    // Subscriptions started before the specified month and still active
                    $query->whereDate('updated_at', '<', $startDate)
                        ->where(function ($query) use ($year, $month) {
                            $query->whereDate('updated_at', '>', Carbon::createFromDate($year, $month, 1)->subDays(30))
                               ;
                        });
                });
            })
            ->paginate(5);

        foreach($dieticianBookings as $booking){
            $dietician_id = $booking->dietician_id;
            // Calculate the end datetime by adding 30 days to the updated_at timestamp
            $endDatetime = Carbon::createFromFormat('Y-m-d H:i:s', $booking->updated_at)->addDays(30);

            $no_of_sent_messages = ChatMessage::where('sender_id', $dietician_id)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();

            $no_of_received_messages = ChatMessage::where('receiver_id', $dietician_id)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();

            $booking->end_datetime = $endDatetime;
            $booking->sent_messages = $no_of_sent_messages;
            $booking->received_messages = $no_of_received_messages;
        }
        return view('admin.dietician.payment_details', compact('dietician', 'dieticianBookings','dieticianPayment'));
    }


    public function makePayment(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'year' => 'required|numeric',
            'month' => 'required|numeric',
            'amount'=>'required|numeric'
        ]);

        if($validator->passes()){

            $dieticianPayment = DieticianSalaryPayment::updateOrCreate([
                'year'=>$request->year,
                'month'=>$request->month,
                'dietician_id'=>$id,
            ],[
                'year'=>$request->year,
                'month'=>$request->month,
                'amount'=>$request->amount,
                'dietician_id'=>$id,

            ]);

            if($dieticianPayment){
                session()->flash('success','Payment done successfully');

                return response()->json([
                    'status' => true,
                    'message'=> 'Payment done successfully.'
                ]);
            }else{
                session()->flash('error','Payment not successful');

                return response()->json([
                    'status' => false,
                    'message'=> 'Payment not successful'
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
