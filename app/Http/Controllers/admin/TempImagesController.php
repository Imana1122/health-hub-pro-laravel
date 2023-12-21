<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class TempImagesController extends Controller
{
    public function create(Request $request){
        $image = $request->image;

        if (!empty($image)) {
            $ext = $image->getClientOriginalExtension();
            $newName = time().'.'.$ext;

            $tempImage = new TempImage();
            $tempImage->name = $newName;
            $tempImage->save();

            $image->move(public_path().'/temp',$newName);

            //Generate thumbnail
            $sourcePath = public_path().'/temp/'.$newName; // Fix the path
            $destPath = public_path().'/temp/thumb/'.$newName; // Fix the path

            $image = Image::make($sourcePath); // Use $sourcePath instead of $tempImage
            $image->fit(300, 275);
            $image->save($destPath);


            return response()->json([
                'status' => true,
                'image_id' => $tempImage->id,
                'imagePath'=> asset('/temp/thumb/'.$newName),
                'message' => 'Image Uploaded successfully'
            ]);
        }
    }

    public function delete(Request $request)
    {
        $imageId = $request->input('id');
            // Find the image in the database
            $image = TempImage::findOrFail($imageId);
            $filename = $image->name;

            $basePath = public_path('/temp/'); // adjust the path based on your folder structure

            // Delete the original image
            $imagePath = $basePath.$filename;
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }

            // Delete the thumbnail image
            $thumbPath = $basePath . 'thumb/' . $filename;
            if (file_exists($thumbPath)) {
                unlink($thumbPath);
            }

            // Delete the image record from the database
            $image->delete();

            return response()->json([
                'status' => true,
                'message' => 'Image deleted successfully',
            ]);

    }
}
