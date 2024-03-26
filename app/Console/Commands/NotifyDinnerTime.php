<?php

namespace App\Console\Commands;

use App\Events\NotificationSent;
use App\Models\MealPlan;
use App\Models\Notification;
use App\Models\Recipe;
use App\Models\User;
use App\Models\UserMealPlan;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class NotifyDinnerTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:notify-dinner-time';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $mealPlans = UserMealPlan::whereDate('created_at', now())->get();

        foreach ($mealPlans as $usermealPlan) {
            $mealPlan= MealPlan::where('id',$usermealPlan->meal_plan_id)->first();

            if ($mealPlan != null) {
                if($mealPlan->dinner != null) {

                $recipe = Recipe::find($mealPlan->dinner);

                if ($recipe) {
                    if (is_array($recipe->images) && count($recipe->images) > 0) {
                        $notification = new Notification([
                            'image' => config('app.url') . '/uploads/recipes/small/' . $recipe->images[0]->image,
                            'message' => $recipe->title . " is to be logged for dinner.",
                        ]);
                    }else{
                        $notification = new Notification([
                            'image' => config('app.url') . '/admin-assets/img/default-150x150.png',
                            'message' => $recipe->title . " is to be logged for dinner.",
                        ]);
                    }
                    $user = User::find($usermealPlan->user_id);

                    if ($user) {

                        $notification->user()->associate($user);
                        $notification->save();
                        $notification=Notification::where('id',$notification->id)->first();
                        $notification->to = 'user';

                        event(new NotificationSent($notification));
                    } else {
                    }
                } else {
                }
            }}
    }}
}
