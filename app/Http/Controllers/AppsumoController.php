<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Integration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\User;
use App\Models\Plan;
use App\Models\Vidpop;
use App\Notifications\UserCreated;
use App\Utils\CommonUtils;

class AppsumoController extends Controller
{
    //
    public function token(Request $request)
    {
        try {
            $user = User::where('email', $request->username)->first();
            if (!$user) {
                return response()->json(['error' => 'User not found!'], 400);
            }
            $token = Auth::attempt(array('email' => $request->username, 'password' => $request->password));
            if ($token) {
                return response()->json(['access' => $token], 200);
            }
            return response()->json(['error' => 'Wrong credential!'], 400);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Wrong credential!'], 400);
        }
    }

    public function notify(Request $request)
    {
        try {
            $action = $request->action;
            $plan_id = $request->plan_id;
            $activation_email = $request->activation_email;
            if ($action == 'activate') {
                $user = User::where('email', $activation_email)->first();
                if ($user) {
                    return response()->json(['error' => 'Email is already exists'], 400);
                }
                $plan = Plan::where('product', $plan_id)->first();
                if (!$plan) {
                    return response()->json(['error' => 'Plan is not exist!'], 400);
                }

                $user = new User();
                $pwd = CommonUtils::randomPassword(8);
                $user->email = $activation_email;
                $user->password = bcrypt($pwd);
                $user->name = 'Appsumo User';
                $user->originator = '0';
                $user->show_tutorial = 1;
                $user->level = $plan->level;
                $user->pay_type = 0;
                $user->credit = 0;
                $user->integration = 0;
                $user->whitelabels = $plan->whitelabels;
                $user->template_admin = 0;
                $user->save();
                return response()->json(['message' => 'Product activated', 'redirect_url' => 'https://vid-gen.com/appsumo/' . $user->id], 201);
            } else if ($action == 'enhance_tier' || $action == 'reduce_tier') {
                $user = User::where('email', $activation_email)->first();
                if (!$user) {
                    return response()->json(['error' => 'Email not found!'], 400);
                }
                $plan = Plan::where('product', $plan_id)->first();
                if (!$plan) {
                    return response()->json(['error' => 'Plan is not exist!'], 400);
                }
                $user->level = $plan->level;
                $user->whitelabels = $plan->whitelabels;
                $user->require_login = 1;
                $user->save();
                if ($action === 'reduce_tier' && $plan_id == 'VidGen_tier1') {
                    // remove sub-users
                    $subusers = User::where('originator', $user->id)->get();
                    foreach ($subusers as $u) {
                        $u->delete();
                    }
                }
                return response()->json(['message' => 'User is successfully upgraded!'], 200);
            } else if ($action == 'refund') {
                $user = User::where('email', $activation_email)->first();
                if (!$user) {
                    return response()->json(['error' => 'Email not found!'], 400);
                }
                // delete sub-users
                $subusers = User::where('originator', $user->id)->get();
                foreach ($subusers as $u) {
                    $u->delete();
                }
                // delete brand
                $brands = Brand::where('user_id', $user->id)->get();
                foreach ($brands as $b) {
                    $b->delete();
                }
                // delete integrations
                $integrations = Integration::where('user_id', $user->id)->get();
                foreach ($integrations as $integration) {
                    $integration->delete();
                }
                // delete VidGens
                $vidpops = Vidpop::where('user_id', $user->id)->get();
                foreach ($vidpops as $vid) {
                    $vid->delete();
                }
                // delete user
                $user->delete();
                return response()->json(['message' => 'User is successfully removed!'], 200);
            } else {
                return response()->json(['error' => 'Invalid Action'], 400);
            }
        } catch (\Throwable $e) {
            Log::error('Appsumo notify: ' . $e->getMessage());
            return response()->json(['error' => 'Server Error!'], 400);
        }
    }

    public function loadUser($id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['error' => 'User not found'], 400);
            } else if (!in_array($user->level, array('TIER1', 'TIER2', 'TIER3'))) {
                return response()->json(['error' => 'User is not from appsumo'], 400);
            }
            return response()->json(['email' => $user->email], 200);
        } catch (\Throwable $e) {
            Log::error('load appsumo user: ' . $e->getMessage());
            return response()->json(['error' => 'User not found'], 400);
        }
    }
}
