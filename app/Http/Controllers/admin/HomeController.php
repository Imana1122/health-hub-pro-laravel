<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class HomeController extends Controller
{
    public function index()
    {


        $currentDateTime = Carbon::now();

        $tempImages = TempImage::where('created_at','<=', $currentDateTime)->get();

        foreach( $tempImages as $image ){
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
        }



        return view("admin.dashboard");
    }

    public function logout() {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
