<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{

    public function edit(Request $request)
    {
        $contact = Contact::first();
        if (empty($contact)) {
            session()->flash('error', 'Contact not found');
            return redirect()->back();
        }

        return view('admin.contact.edit', compact('contact'));
    }

    public function update(Request $request)
    {
        $contact = Contact::first();
        if (empty($contact)) {
            session()->flash('error', 'Contact not found');
            return response()->json([
                'status' => false,
                'notFound' => true,
                'message' => 'Contact not found'
            ]);
        }

        $validator = Validator::make($request->all(), [
            "corporate_office" => "required",
            "email" => "required|email",
            "phone_number" => "required",
            "mobile_number" => "required"
        ]);

        if ($validator->passes()) {
            $contact->update([
                'corporate_office' => $request->corporate_office,
                'email' => $request->email,
                'phone_number' => $request->phone_number,
                'mobile_number' => $request->mobile_number,
            ]);

            session()->flash("success", "Contact updated successfully");

            return response()->json([
                "status" => true,
                "message" => 'Contact updated successfully'
            ]);
        } else {
            return response()->json([
                "status" => false,
                "errors" => $validator->errors()
            ]);
        }
    }

    public function getAll(Request $request){
        $contact = Contact::latest()->first();




        return response()->json([
            'status' => true,
            'data' =>  $contact
        ]);
    }
}
