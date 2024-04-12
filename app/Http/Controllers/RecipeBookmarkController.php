<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\RecipeBookmark;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RecipeBookmarkController extends Controller
{
    public function bookmark(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'recipe_id' => 'required',
        ]);

        if ($validation->fails()) {
            return response()->json([
                'errors' => $validation->errors(),
                'status' => false
            ]);
        } else {
            // Check if the rating already exists
            $existingBookmark = RecipeBookmark::where('user_id', auth()->user()->id)
                ->where('recipe_id', $request->recipe_id)
                ->first();

            if ($existingBookmark) {
                $message = 'Bookmark removed from '.$existingBookmark->recipe->title;
                RecipeBookmark::where('user_id', auth()->user()->id)
                ->where('recipe_id', $request->recipe_id)->delete();

            } else {
                // Create a new rating
                $newBookmark = new RecipeBookmark([
                    'user_id' => auth()->user()->id,
                    'recipe_id' => $request->recipe_id,

                ]);
                $newBookmark->save();
                $message = $newBookmark->recipe->title . ' bookmarked successfully!';

            }


            return response()->json([
                'message' => $message,
                'status' => true
            ]);
        }
    }

    public function index(Request $request){
        $bookmarkedRecipes = RecipeBookmark::where('user_id',auth()->user()->id)->pluck('recipe_id');
        $recipes = Recipe::whereIn('id',$bookmarkedRecipes)->with('images')->with('meal_type');

        if ($request->get('keyword') != '') {
            $recipes = $recipes->where('title', 'like', '%' . $request->input('keyword') . '%');
        }

        $recipes = $recipes->paginate(5);

        return response()->json([
            'status'=>true,
            'data'=>$recipes
        ]);
    }
}
