<?php

namespace App\Http\Controllers\dietician;

use App\Http\Controllers\Controller;
use App\Models\Progress;
use DateTime;
use Illuminate\Http\Request;

class ShareProgressController extends Controller
{
    public function index($id){
        $progress = Progress::where('user_id',$id)->paginate(10);
        return response()->json([
            'status'=>true,
            'data'=>$progress
        ]);
    }

    public function result(Request $request,$id)
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

        $progress1 = Progress::where('user_id', $id)
                            ->whereYear('updated_at', $year1)
                            ->whereMonth('updated_at', $month1)
                            ->first();

        $progress2 = Progress::where('user_id', $id)
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

    public function stat(Request $request,$id)
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

        $progress1 = Progress::where('user_id', $id)
                            ->whereYear('updated_at', $year1)
                            ->whereMonth('updated_at', $month1)
                            ->first();

        $progress2 = Progress::where('user_id', $id)
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

    public function getLineChartData(Request $request,$id)
    {
        $year = $request->get('year');
        if ($year == null) { // Use comparison operator '==' instead of assignment operator '='
            $year = now()->year;
        }

        // Fetch progress data for weight and height along with the corresponding months
        $progressData = Progress::where('user_id', $id)->whereYear('updated_at',$year)
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
