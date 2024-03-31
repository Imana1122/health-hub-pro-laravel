<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Dietician;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DieticianController extends Controller
{
    public function index(Request $request){
        $dieticians = Dietician::where('approved_status',1)->latest();
        if($request->get('keyword')){
            if (!empty($request->get('keyword'))) {
                $dieticians = $dieticians->where('name','like','%'.$request->get('keyword').'%');
            }
        }

        $dieticians = $dieticians->paginate(10);

        return view("admin.dietician.list", compact('dieticians'));
    }

    public function getUnApprovedDieticians(Request $request){
        $dieticians = Dietician::where('approved_status',0)->latest();
        if($request->get('keyword')){
            if (!empty($request->get('keyword'))) {
                $dieticians = $dieticians->where('name','like','%'.$request->get('keyword').'%');
            }
        }

        $dieticians = $dieticians->paginate(10);

        return view("admin.dietician.list", compact('dieticians'));
    }

    public function detail($id){
        $dietician = Dietician::where('id',$id)->first();
        return view('admin.dietician.detail',compact('dietician'));
    }

    public function approveStatus($id){
        $dietician = Dietician::where('id',$id)->first();
        if($dietician){
            // Generate a random string of length 8
            $dietician->approved_status = 1;
            $dietician->status = 1;
            $dietician->save();
            $message = "You have been approved as dietician";
            $this->sendcode($dietician->phone_number,$message);
            session()->flash('success','Dietician approved successfully');
            return response()->json([
                'status' => true,
                'message' => 'Dietician approved successfully'
            ]);


        }else{
            session()->flash('success','Dietician not found');
            return response()->json([
                'status' => true,
                'message' => 'Dietician not found'
            ]);
        }
    }

    public function destroy($dieticianId, Request $request){
        $dietician =  Dietician::find($dieticianId);
        if(empty($dietician)){
            session()->flash('error','Dietician not found');
            return response()->json([
                'status' => false,
                'message' => 'Dietician not found'
            ]);
        }

        // Check if dietician has an existing image
        if (!empty($dietician->image)) {
            // Remove the previous image and thumbnail (if they exist)
            $oldImagePath = public_path('/uploads/dietician/profile/' . $dietician->image);
            $oldThumbnailPath = public_path('/uploads/dietician/profile/thumb/' . $dietician->image);

            if (file_exists($oldImagePath)) {
                unlink($oldImagePath); // Delete the old image
            }

            if (file_exists($oldThumbnailPath)) {
                unlink($oldThumbnailPath); // Delete the old thumbnail
            }
        }

        // Check if dietician has an existing cv
        if (!empty($dietician->cv)) {
            // Remove the previous cv and thumbnail (if they exist)
            $oldImagePath = public_path('/uploads/dietician/cv/' . $dietician->cv);
            $oldThumbnailPath = public_path('/uploads/dietician/cv/thumb/' . $dietician->cv);

            if (file_exists($oldImagePath)) {
                unlink($oldImagePath); // Delete the old cv
            }

            if (file_exists($oldThumbnailPath)) {
                unlink($oldThumbnailPath); // Delete the old thumbnail
            }
        }

        $dietician->delete();

        session()->flash('success','Dietician deleted successfully');
        return response()->json([
            'status' => true,
            'message' => 'Dietician deleted successfully'
        ]);

    }

    public function sendcode($phone_number,$message)
    {
        try {
            $client = new Client();
            $response = $client->post('https://sms.aakashsms.com/sms/v3/send', [
                'form_params' => [
                    'auth_token' => 'c1eecbd817abc78626ee119a530b838ef57f8dad9872d092ab128776a00ed31d',
                    'to' => $phone_number,
                    'text' => $message,
                ],
            ]);

            if ($response->getStatusCode() === 200) {
                $message = 'SMS sent to dietician successfully';
                return response()->json([
                    'status'=> true,
                    'message'=> $message
                ]);
            } else {
                return response()->json([
                    'status'=> false,
                    'error'=> 'Failed to send SMS.'
                ]);
            }
        } catch (\Exception $e) {
            $message = 'Failed to send SMS. Check your Internet Connection.';
            return response()->json([
                'status'=> false,
                'errors'=> $message
            ]);
        }
    }
}
