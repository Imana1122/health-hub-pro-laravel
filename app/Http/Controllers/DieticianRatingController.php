<?php

namespace App\Http\Controllers;

use App\Models\DieticianRating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DieticianRatingController extends Controller
{
    public function saveRating(Request $request, $id)
{
    $validation = Validator::make($request->all(), [
        'rating' => 'required|numeric|min:1|max:5',
        'comment' => 'required|max:250',
    ]);

    if ($validation->fails()) {
        return response()->json([
            'errors' => $validation->errors(),
            'status' => false
        ]);
    } else {
        // Check if the rating already exists
        $existingRating = DieticianRating::where('user_id', auth()->user()->id)
            ->where('dietician_id', $id)
            ->first();

        if ($existingRating) {
            // Update the existing rating
            DieticianRating::where('user_id', auth()->user()->id)
            ->where('dietician_id', $id)
            ->update([
                'comment' => $request->comment,
                'rating' => $request->rating,
                'status' => 1
            ]);

        } else {
            // Create a new rating
            $newRating = new DieticianRating([
                'user_id' => auth()->user()->id,
                'dietician_id' => $id,
                'comment' => $request->comment,
                'rating' => $request->rating,
                'status' => 1
            ]);
            $newRating->save();
        }

        $message = 'Thanks for your rating';

        return response()->json([
            'message' => $message,
            'status' => true
        ]);
    }
}

}
