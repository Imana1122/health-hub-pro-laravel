<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\SubCategory;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with(['category', 'brand', 'subCategory','images']);

        if ($request->get('keyword')) {
            $products = $products->where('products.title', 'like', '%' . $request->get('keyword') . '%');
        }

        $products = $products->paginate(10);

        return view("admin.products.list", compact('products'));
    }

    public function create(){
        $categories = Category::orderBy('name','ASC')->get();
        $subCategories = [];
        $brands = Brand::orderBy('name','ASC')->get();

        return view("admin.products.create", compact('categories','subCategories','brands'));
    }

    public function store(Request $request){

        $rules =  [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'is_featured' => 'required|in:Yes,No',
            'sku' => 'required|string|max:255|unique:products',
            'barcode' => 'nullable|string|max:255',
            'track_qty' => 'required|in:Yes,No',
            'status' => 'required|in:0,1',
        ] ;


        if (!empty($request->track_qty) && $request->track_qty == 'Yes'){
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(),$rules);

        if ($validator->passes()){
            $product = Product::create(
                $request->only(
                    'title',
                    'slug',
                    'description',
                    'price',
                    'compare_price',
                    'category_id',
                    'sub_category_id',
                    'brand_id',
                    'is_featured',
                    'sku',
                    'barcode',
                    'track_qty',
                    'qty',
                    'status',
                )
            );
            $productId = $product->id;

            // Check if category has an existing image
            if (!empty($product->image)) {
                // Remove the previous image and thumbnail (if they exist)
                $oldImagePath = public_path('/uploads/category/' . $product->$product);
                $oldThumbnailPath = public_path('/uploads/category/thumb/' . $product->image);

                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath); // Delete the old image
                }

                if (file_exists($oldThumbnailPath)) {
                    unlink($oldThumbnailPath); // Delete the old thumbnail
                }
            }

            //Save Gallery Pics
            if(!empty($request->image_array)){
                foreach ($request->image_array as $temp_image_id){

                    $tempImageInfo = TempImage::find($temp_image_id);
                    $extArray = explode('.',$tempImageInfo->name);
                    $ext = last($extArray); //like jpg, gif, png, jpeg

                    $productImage = new ProductImage();
                    $productImage->product_id = $productId;
                    $productImage->image_id = 'NULL';
                    $productImage->save();

                    $imageName = $productId.'-'.$productImage->id.'-'.time().'.'.$ext;
                    $productImage->image = $imageName;
                    $productImage->save();

                    //Generate Product Thumbnail
                    //Large Image
                    $sourcePath = public_path().'/temp/'.$tempImageInfo->name;
                    $destinationPath = public_path().'/uploads/products/large/'.$imageName;
                    $image = Image::make($sourcePath);
                    $image->resize(1400,null, function ($constraint) {
                        $constraint->aspectRatio();
                    });
                    $image->save($destinationPath);

                    //Small Image
                    $sourcePath = public_path().'/temp/'.$tempImageInfo->name;
                    $destinationPath = public_path().'/uploads/products/small/'.$imageName;
                    $image = Image::make($sourcePath);
                    $image->fit(300,300);
                    $image->save($destinationPath);

                    // Delete the temporary image
                    $tempThumbPath = public_path().'/temp/thumb/'.$tempImageInfo->name;
                    if (file_exists($sourcePath)) {
                        unlink($sourcePath);
                    }
                    if (file_exists($tempThumbPath)) {
                        unlink($tempThumbPath);
                    }

                    // Delete the temporary image record from the database
                    $tempImageInfo->delete();
                }


            }

            $request->session()->flash('success','Product created successfully');

            return response()->json([
                'status' => true,
                'message'=> 'Product created successfully.'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }


    }


    public function edit($productId, Request $request){

        $product = Product::find($productId);

        if(empty($product)){
            $request->session()->flash('error','Product not found');

            return redirect()->route('products.index');
        }

        $images = $product->images;

        $categories = Category::orderBy('name','ASC')->get();
        $subCategories = $product->category->subCategories;
        $brands = Brand::orderBy('name','ASC')->get();

        return view("admin.products.edit", compact('categories','subCategories','brands', 'product','images'));
    }

    public function update($productId, Request $request){
        $product = Product::find($productId);
        if(empty($product)){
            $request->session()->flash('error','Product not found');


            return redirect()->route('products.index')->with('error','Product not found');
        }



        $rules =  [
            'title' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:products,slug,'.$product->id.',id',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_price' => 'nullable|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:sub_categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'is_featured' => 'required|in:Yes,No',
            'sku' => 'required|string|max:255|unique:products,sku,'.$product->id.',id',
            'barcode' => 'nullable|string|max:255',
            'track_qty' => 'required|in:Yes,No',
            'status' => 'required|in:0,1',
        ] ;


        if (!empty($request->track_qty) && $request->track_qty == 'Yes'){
            $rules['qty'] = 'required|numeric';
        }

        $validator = Validator::make($request->all(),$rules);

        if ($validator->passes()){
            $product->update(
                $request->only(
                    'title',
                    'slug',
                    'description',
                    'price',
                    'compare_price',
                    'category_id',
                    'sub_category_id',
                    'brand_id',
                    'is_featured',
                    'sku',
                    'barcode',
                    'track_qty',
                    'qty',
                    'status',
                )
            );

            $request->session()->flash('success','Product updated successfully');

            return response()->json([
                'status' => true,
                'message'=> 'Product updated successfully.'
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }


    }

    public function destroy($productId, Request $request){
        $product = Product::find($productId);

        if(empty($product)){
            $request->session()->flash('error','Product not found');
            return response()->json([
                'status'=> false,
                'notFound'=> true,
                'error'=> 'Product not found'
            ]);
        }

        $images = $product->images;

        if (!empty($images)){
            foreach ($images as $image){
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
            }
            $product->images()->delete();
        }

        $product->delete();
        $request->session()->flash('success','Product deleted successfully');
        return response()->json([
            'status'=> true,
            'message'=> 'Product Deleted Successfully'
        ]);
    }
}
