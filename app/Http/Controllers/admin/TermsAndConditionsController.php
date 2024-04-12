<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TermsAndConditions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TermsAndConditionsController extends Controller
{
    public function index(Request $request){
        $termsAndConditions = TermsAndConditions::latest();

        if ($request->get('keyword')) {
            if (!empty($request->get('keyword'))) {
                $termsAndConditions = $termsAndConditions->where('name', 'like', '%' . $request->get('keyword') . '%');
            }
        }

        $termsAndConditions = $termsAndConditions->paginate(10);

        return view("admin.termsAndConditions.list", compact('termsAndConditions'));
    }

    public function create(){

        return view('admin.termsAndConditions.create');
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'content'=> 'required',

        ]);

        if ($validator->passes()){
            $allergen = TermsAndConditions::create(
                $request->only('content'
            ));

            session()->flash('success','TermsAndConditions created successfully');

            return response()->json([
                'status' => true,
                'message'=> 'TermsAndConditions created successfully.'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }


    public function destroy($allergenId){
        $allergen =  TermsAndConditions::find($allergenId);
        if(empty($allergen)){
            session()->flash('error','TermsAndConditions not found');
            return response()->json([
                'status' => false,
                'message' => 'TermsAndConditions not fpund'
            ]);
        }

        $allergen->delete();

        session()->flash('success','TermsAndConditions deleted successfully');
        return response()->json([
            'status' => true,
            'message' => 'TermsAndConditions deleted successfully'
        ]);

    }

    public function getAll(Request $request){
        $termsAndConditions = TermsAndConditions::paginate(10);




        return response()->json([
            'status' => true,
            'data' =>  $termsAndConditions
        ]);
    }
}
