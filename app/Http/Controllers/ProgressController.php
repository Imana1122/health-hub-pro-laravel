<?php

namespace App\Http\Controllers;

use App\Models\Progress;
use App\Models\UserProfile;
use Carbon\Carbon;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProgressController extends Controller
{
    public function index(){
        $progress = Progress::where('user_id',auth()->user()->id)->latest()->paginate(10);
        return response()->json([
            'status'=>true,
            'data'=>$progress
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "weight"=> "required|numeric",
            "height"=> "required|numeric",
            'front_image'=>'required|image',
            'back_image'=>'required|image',
            'right_image'=>'required|image',
            'left_image'=>'required|image',
        ]);

        if ($validator->passes()) {
            $user = auth()->user();

            // Check if progress exists for the current month and user
            $progress = Progress::where('user_id', $user->id)
                ->whereYear('created_at', Carbon::now()->year)
                ->whereMonth('created_at', Carbon::now()->month)
                ->first();
            $userProfile = UserProfile::where('user_id',auth()->user()->id)->first();

            if ($progress) {
                // Update existing progress
                $progress->weight = $request->weight;
                $progress->height = $request->height;
                $progress->updated_at=now();

            } else {
                // Create new progress
                $progress = new Progress();
                $progress->user_id = $user->id;
                $progress->weight = $request->weight;
                $progress->height = $request->height;
            }
            $userProfile->updated_at=now();
            $userProfile->height=$request->height;
            $userProfile->weight=$request->weight;
            $userProfile->save();

            // Save or update progress images
            $progress->front_image = $this->saveImage($request->front_image, $user->id, 'front');
            $progress->back_image = $this->saveImage($request->back_image, $user->id, 'back');
            $progress->right_image = $this->saveImage($request->right_image, $user->id, 'right');
            $progress->left_image = $this->saveImage($request->left_image, $user->id, 'left');
            $progress->save();

            return response()->json([
                "status"=> true,
                "message"=> $progress ? 'Progress updated successfully' : 'Progress added successfully'
            ]);
        } else {
            return response()->json([
                "status"=> false,
                "errors"=> $validator->errors()
            ]);
        }
    }

    private function saveImage($image, $userId, $side)
    {
        $ext = $image->getClientOriginalExtension();
        $timestamp = now()->format('Y-m-d_His');
        $newName = $userId . '_' . $timestamp . '.' . $ext;
        $imagePath = $image->store("public/uploads/progress/$side");
        Storage::move($imagePath, "public/uploads/progress/$side/$newName");
        return $newName;
    }


    public function result(Request $request)
    {
        // Convert string representations of dates to DateTime objects
        $month1 = new DateTime($request->get('month1'));
        $month2 = new DateTime($request->get('month2'));

        // Check if the conversion is successful before accessing properties
        if ($month1 && $month2) {
            // Access the "year" property
            $year1 = $month1->format('Y');
            $year2 = $month2->format('Y');

            // Access the "month" property
            $month1 = $month1->format('m');
            $month2 = $month2->format('m');
        }else{
            return response()->json([
                'status'=>false,
                'message'=>'Incomplete data provided!'
            ]);
        }

        $progress1 = Progress::where('user_id', auth()->user()->id)
                            ->whereYear('updated_at', $year1)
                            ->whereMonth('updated_at', $month1)
                            ->first();

        $progress2 = Progress::where('user_id', auth()->user()->id)
                            ->whereYear('updated_at', $year2)
                            ->whereMonth('updated_at', $month2)
                            ->first();

        $data = [
            [
                "title" => "Front Facing",
                "month_1_image" => $progress1 != null ? asset('storage/uploads/progress/front/'.$progress1->front_image) : asset('admin-assets/img/default-150x150.png'),
                "month_2_image" => $progress2 != null ? asset('storage/uploads/progress/front/'.$progress2->front_image) : asset('admin-assets/img/default-150x150.png'),
            ],
            [
                "title" => "Back Facing",
                "month_1_image" => $progress1 != null ? asset('storage/uploads/progress/back/'.$progress1->back_image) : asset('admin-assets/img/default-150x150.png'),
                "month_2_image" => $progress2 != null ? asset('storage/uploads/progress/back/'.$progress2->back_image) : asset('admin-assets/img/default-150x150.png'),
            ],
            [
                "title" => "Left Facing",
                "month_1_image" => $progress1 != null ? asset('storage/uploads/progress/left/'.$progress1->left_image) : asset('admin-assets/img/default-150x150.png'),
                "month_2_image" => $progress2 != null ? asset('storage/uploads/progress/left/'.$progress2->left_image) : asset('admin-assets/img/default-150x150.png'),
            ],
            [
                "title" => "Right Facing",
                "month_1_image" => $progress1 != null ? asset('storage/uploads/progress/right/'.$progress1->right_image) : asset('admin-assets/img/default-150x150.png'),
                "month_2_image" => $progress2 != null ? asset('storage/uploads/progress/right/'.$progress2->right_image) : asset('admin-assets/img/default-150x150.png'),
            ],
        ];

        return response()->json([
            'status'=>true,
            'data'=>$data
        ]);
    }

    public function stat(Request $request)
    {
        // Convert string representations of dates to DateTime objects
        $month1 = new DateTime($request->get('month1'));
        $month2 = new DateTime($request->get('month2'));

        // Check if the conversion is successful before accessing properties
        if ($month1 && $month2) {
            // Access the "year" property
            $year1 = $month1->format('Y');
            $year2 = $month2->format('Y');

            // Access the "month" property
            $month1 = $month1->format('m');
            $month2 = $month2->format('m');
        }else{
            return response()->json([
                'status'=>false,
                'message'=>'Incomplete data provided!'
            ]);
        }

        $progress1 = Progress::where('user_id', auth()->user()->id)
                            ->whereYear('updated_at', $year1)
                            ->whereMonth('updated_at', $month1)
                            ->first();

        $progress2 = Progress::where('user_id', auth()->user()->id)
                            ->whereYear('updated_at', $year2)
                            ->whereMonth('updated_at', $month2)
                            ->first();

        $statArr = [];

        // Calculate Lose Weight Percentage
        if ($progress1 && $progress2) {
            $loseWeightDiff = $progress1->weight - $progress2->weight;
            $loseWeightPer = ($loseWeightDiff / $progress1->weight) * 100;
            $statArr[] = [
                "title" => "Lose Weight",
                "diff_per" => abs($loseWeightPer), // Use abs to ensure positive percentage
                "month_1_per" => "{$loseWeightPer}%",
                "month_2_per" => "{$progress2->weight}kg",
            ];
        }

        // Calculate Gain Weight Percentage
        if ($progress1 && $progress2) {
            $gainWeightDiff = $progress2->weight - $progress1->weight;
            $gainWeightPer = ($gainWeightDiff / $progress1->weight) * 100;
            $statArr[] = [
                "title" => "Gain Weight",
                "diff_per" => abs($gainWeightPer), // Use abs to ensure positive percentage
                "month_1_per" => "{$progress1->weight}kg",
                "month_2_per" => "{$gainWeightPer}%",
            ];
        }

        return response()->json([
            'status'=>true,
            'data'=>$statArr
        ]);
    }

    public function getLineChartData(Request $request)
    {
        $year=$request->get('year');
        if($year==null){
            $year=now()->year;
        }
        // Fetch progress data for weight and height along with the corresponding months
        $progressData = Progress::where('user_id', auth()->user()->id)->whereYear('updated_at',$year)
            ->orderBy('updated_at') // Ensure data is ordered by date
            ->get(['weight', 'height', 'updated_at']) // Select weight, height, and updated_at fields
            ->map(function ($progress) {
                return [
                    'month' => $progress->updated_at->format('n'), // Extract month from updated_at
                    'weight' => $progress->weight,
                    'height' => $progress->height
                ];
            });

        return response()->json([
            'status' => true,
            'data' => $progressData
        ]);
    }




}
