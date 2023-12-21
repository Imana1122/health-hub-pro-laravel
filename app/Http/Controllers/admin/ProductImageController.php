<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;

class ProductImageController extends Controller
{
    public function update(Request $request)
    {
        $image = $request->image;
        $ext = $image->getClientOriginalExtension();
        $sourcePath = $image->getPathName();

        $productId = $request->product_id;

        $productImage = new ProductImage();
        $productImage->product_id = $productId;
        $productImage->image = 'NULL';
        $productImage->save();

        $imageName = $productId.'-'.$productImage->id.'-'.time().'.'.$ext;
        $productImage->image = $imageName;
        $productImage->save();


        //Generate Product Thumbnail
        //Large Image
        $destinationPath = public_path().'/uploads/products/large/'.$imageName;
        $image = Image::make($sourcePath);
        $image->resize(1400,null, function ($constraint) {
            $constraint->aspectRatio();
        });
        $image->save($destinationPath);

        //Small Image
        $destinationPath = public_path().'/uploads/products/small/'.$imageName;
        $image = Image::make($sourcePath);
        $image->fit(300,300);
        $image->save($destinationPath);

        return response()->json([
            'status' => true,
            'image_id' => $productImage->id,
            'imagePath' => asset('uploads/products/small/'.$productImage->image),
            'message' => 'Image saved successfully'
        ]);

    }

    public function delete(Request $request)
    {
        $imageId = $request->id;
        // Find the image in the database
        $image = ProductImage::findOrFail($imageId);
        if(empty($image)){
            return response()->json([
                'status' => false,
                'message' => 'Image not found'
            ]);
        }
        $filename = $image->image;

        $basePath = public_path('uploads/products/'); // adjust the path based on your folder structure

        // Delete the large image
        $imagePath = $basePath.'large/'.$filename;
        if (file_exists($imagePath)) {
            unlink($imagePath);
        }

        // Delete the small image
        $smallPath = $basePath . 'small/' . $filename;
        if (file_exists($smallPath)) {
            unlink($smallPath);
        }

        // Delete the image record from the database
        $image->delete();

        return response()->json([
            'status' => true,
            'message' => 'Image deleted successfully',
        ]);

    }
}
