<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\Plan;
use App\Notifications\UserCreated;
use App\User;
use App\Utils\CommonUtils;

class PaymentController extends Controller
{
    //
    public function paddleCB(Request $request)
    {
        try {
            $alert_name = $request->alert_name;
            $email = $request->email;
            $plan_id = $request->subscription_plan_id;

            Log::info($plan_id);
            Log::info($alert_name);
            Log::info($email);

            if ($alert_name == 'subscription_payment_succeeded' || $alert_name === 'subscription_payment_created' || $alert_name === 'subscription_created') {
                $plan = Plan::where('product', $plan_id)->first();
                if (!$plan) {
                    return response()->json(['error' => 'Plan not found on table!'], 400);
                }
                $user = User::where('email', $email)->first();
                if (!$user) {
                    $pwd = CommonUtils::generateCode(8);
                    $user = new User();
                    $user->email = $email;
                    $user->name = $email;
                    $user->password = bcrypt($pwd);
                    $user->originator = '0';
                    $user->level = $plan->level;
                    $user->pay_type = 0;
                    $user->credit = 0;
                    $user->integration = 0;
                    if ($alert_name === 'subscription_created') {
                        $user->cancel_url = $request->cancel_url;
                        $user->subscribe = $request->subscription_id;
                    }
                    if ($plan->level === 'OTO2') {
                        $user->whitelabels = 10;
                    } else {
                        $user->whitelabels = 0;
                    }
                    $user->template_admin = 0;
                    $user->show_tutorial = 1;
                    $user->save();
                    $user->notify(new UserCreated($email, $pwd, $plan->id));
                    CommonUtils::addMailwizz($user->email, $user->name, '');
                } else {
                    $user->level = $plan->level;
                    if ($alert_name === 'subscription_created') {
                        $user->cancel_url = $request->cancel_url;
                        $user->subscribe = $request->subscription_id;
                    }
                    $user->save();
                }
                return response()->json(['msg' => 'Success'], 200);
            } else if ($alert_name == 'subscription_cancelled' || $alert_name === 'subscription_payment_failed' || $alert_name === 'subscription_payment_refunded') {
                $user = User::where('email', $email)->first();
                if (!$user) {
                    return response()->json(['error' => 'User not found on table!'], 400);
                }
                $user->level = 'FET';
                $user->cancel_url = null;
                $user->subscribe = null;
                $user->save();
                return response()->json(['msg' => 'Success'], 200);
            } else if ($alert_name == 'payment_succeeded') {
                // this is for oto1
                $plan = Plan::where('product', $request->product_id)->first();
                if (!$plan) {
                    return response()->json(['error' => 'Plan not found on table!'], 400);
                }
                $user = User::where('email', $email)->first();
                if (!$user) {
                    $pwd = CommonUtils::generateCode(8);
                    $user = new User();
                    $user->email = $email;
                    $user->name = $email;
                    $user->password = bcrypt($pwd);
                    $user->originator = '0';
                    $user->level = $plan->level;
                    $user->pay_type = 0;
                    $user->credit = 0;
                    $user->integration = 0;
                    $user->template_admin = 0;
                    $user->whitelabels = 0;
                    $user->subscribe = $request->order_id;
                    $user->show_tutorial = 1;
                    $user->save();
                    $user->notify(new UserCreated($email, $pwd, $plan->id));
                    CommonUtils::addMailwizz($user->email, $user->name, '');
                } else {
                    $user->level = $plan->level;
                    $user->save();
                }

            }
        } catch (\Throwable $e) {
            Log::error('paddle callback: ' . $e->getMessage());
        }
    }

    public function updatePlan()
    {
        try {
        } catch (\Throwable $e) {
            Log::error('padle updatePlan: ' . $e->getMessage());
            return response()->json(['error' => 'Server erorr'], 400);
        }
    }
}
