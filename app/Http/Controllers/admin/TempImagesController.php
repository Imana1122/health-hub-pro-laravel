<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManager;

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

            $image = ImageManager::gd()->read($sourcePath);
            $image->resize(300, 275);
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
            $path = public_path('/temp/'. $image->name);
            $thumbPath = public_path('/temp/thumb/'. $image->name);

            //Delete main image
            if( File::exists( $path ) ){
                File::delete( $path );
            }

            if( File::exists( $thumbPath ) ){
                File::delete( $thumbPath );
            }

            TempImage::where('id',$image->id)->delete();

            return response()->json([
                'status' => true,
                'message' => 'Image deleted successfully',
            ]);

    }
}
