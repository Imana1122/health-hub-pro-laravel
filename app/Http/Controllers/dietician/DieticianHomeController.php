<?php

namespace App\Http\Controllers\Dietician;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\Dietician;
use App\Models\DieticianBooking;
use App\Models\DieticianRating;
use App\Models\DieticianSalaryPayment;
use Illuminate\Http\Request;

class DieticianHomeController extends Controller
{
    public function index(Request $request){


        // Step 1: Query UserWorkoutpayment to retrieve the payments of workouts paymentged by the user
        $dieticianPaymentHistory = DieticianSalaryPayment::where('dietician_id', auth()->user()->id)->get();
        if ($dieticianPaymentHistory->isEmpty()) {
            return response()->json([
                'status' => true,
                'message' => 'No dietician payments found'
            ]);
        }
        // Step 3: Group the data based on the $type parameter
        $lineChartData = [];
        foreach ($dieticianPaymentHistory as $payment) {
            $month = $payment->year . '-'.$payment->month;
            $amount = $payment->amount;
            if (!isset($lineChartData[$month])) {
                $lineChartData[$month] = 0;
            }
            $lineChartData[$month] += $amount;
        }
        $formattedData = [];
        foreach ($lineChartData as $month => $amount) {
            $formattedData[] = ['x' => $month, 'y' => $amount];
        }
        // Return the line chart data to your frontend
        return response()->json([
            'status' => true,
            'data' => $formattedData
        ]);
    }

    public function getPaymentDetails(Request $request){
        $year = $request->get('year');
        $month = $request->get('month');


        $dietician = Dietician::findOrFail(auth()->user()->id);
        $dieticianPayment = DieticianSalaryPayment::where('dietician_id',$dietician->id)->where('year',$year)->where('month',$month)->first();
        $dieticianBookings = DieticianBooking::with('user')->where('dietician_id', $dietician->id)
            ->whereYear('updated_at', $year)
            ->whereMonth('updated_at', $month)
            ->where('payment_status', true)
            ->paginate(5);

        foreach($dieticianBookings as $booking){
            $dietician_id = $booking->dietician_id;
            $no_of_sent_messages = ChatMessage::where('sender_id', $dietician_id)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();

            $no_of_received_messages = ChatMessage::where('receiver_id', $dietician_id)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();

            $booking->sent_messages = $no_of_sent_messages;
            $booking->received_messages = $no_of_received_messages;
        }

        return response()->json([
            'status'=>true,
            'data'=>[
                'dietician_payment'=>$dieticianPayment,
                'dieticianBookings'=>$dieticianBookings]
        ]);
    }

    public function getPayments(){
        $payments=DieticianSalaryPayment::where('dietician_id',auth()->user()->id)->orderBy('year')->orderBy('month')->paginate(10);
        return response()->json([
            'status'=>true,
            'data'=>$payments
        ]);
    }


    public function getRatings(){
        $payments=DieticianRating::with('user')->where('dietician_id',auth()->user()->id)->latest()->paginate(10);
        return response()->json([
            'status'=>true,
            'data'=>$payments
        ]);
    }

}
