<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Recipe;
use App\Models\RecipeStep;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RecipeStepController extends Controller
{
    public function index(){

    }

    public function create(){

        $recipes = Recipe::orderBy('title','asc')->get();
        return view("admin.recipe-steps.create",compact("recipes"));
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            "recipe_id"=> "required",
            "steps"=> "required|array",
        ]);

        if ($validator->fails()){
            return back()->withErrors($validator)->withInput();
        }else{
            foreach($request->steps as $step){
                RecipeStep::create([
                    "recipe_id"=> $request->recipe_id,
                    "content"=> $step
                ]);
            }

            $message = "Recipe steps created successfully";

            session()->flash("message", $message);

            return response()->json([
                "status"=> true,
                "message"=> $message
            ]);
        }
    }


}
