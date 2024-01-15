<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\User;
use App\Models\Coupon;
use App\Models\Plan;
use App\Notifications\UserCreated;
use App\Utils\CommonUtils;

class AuthController extends Controller
{
    //

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function login(Request $request)
    {
        $user = User::where('email', request('email'))->first();
        if (!$user) {
            return response()->json(['error' => 'User not found, Check your credential again!'], 401);
        }

        $input = $request->all();
        $goto = $user->require_login == 0 ? ($user->role == 'admin' ? 'admin-payout' : 'statistics') : 'memberships';
        $user->require_login = 0;
        $user->save();

        $this->validate($request, [
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($token = Auth::attempt(array('email' => $input['email'], 'password' => $input['password']))) {
            if (Auth::user()->approved == 0)
                return response()->json(['title' => 'Unapproved', 'msg' => 'Please wait until your account will be approved!'], 401);    
            return $this->respondWithToken($token, false, $goto);
        }

        return response()->json(['title' => 'Unauthorized', 'msg' => 'Password incorrect, please try again or get new password!'], 401);
    }

    public function logout()
    {
        Auth::logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    protected function respondWithToken($token, $isNew, $goto = 'statistics')
    {
        $storage = 0;
        $originator = Auth::user()->originator;
        $origin_user = null;

        if (!$originator || ($originator && strval($originator) == '0')) {
            $used = 0;
            if (!$isNew) {
                $used = CommonUtils::s3Size();
                $user = User::find(Auth::user()->id);
                $user->s3 = $used;
                $user->save();
            }
            $plan = Plan::where('level', Auth::user()->level)->first();
            $storage = $plan->storage - $used;
        } else {
            $origin_user = User::find($originator);
        }
        $res_user = Auth::user();
        $deep = CommonUtils::getDeepKey();
        if ($deep) {
            $res_user['deep_api'] = $deep['api'];
            $res_user['deep_secret'] = $deep['key'];
        } else {
            $res_user['deep_api'] = '';
            $res_user['deep_secret'] = '';
        }
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => Auth::factory()->getTTL() * 3600,
            'user' => $res_user,
            's3' => $storage,
            'origin_level' => $origin_user ? $origin_user->level : '',
            'goto' => $goto
        ]);
    }

    public function guard()
    {
        return Auth::guard();
    }

    public function register(Request $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            if ($user) {
                return response()->json(['error' => 'Email is alrady registered'], 400);
            }
            $user = new User();
            $user->email = $request->email;
            $user->name = $request->name;
            $user->role  = $request->role;
            $user->password = bcrypt($request->password);
            $user->originator = '0';
            $user->level = 'FET';
            $user->pay_type = 0;
            $user->credit = 0;
            $user->integration = 0;
            $user->whitelabels = 0;
            $user->template_admin = 0;
            $user->balance = 0;
            $user->save();
            $plan = Plan::where('level', 'FET')->first();
            $user->show_tutorial = 1;
            // $user->notify(new UserCreated($request->email, $request->password, $plan->id));
            // CommonUtils::addMailwizz($user->email, $user->name, '');
            if ($token = Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                // return $this->respondWithToken($token, true, 'statistics');
                return response()->json(['title' => 'Registered & Unapproved', 'msg' => 'Your account is registered. Please wait until it will be approved!', 'icon'=>'success'], 401);    
            }
        } catch (\Throwable $e) {
            Log::error('register: ' . $e->getMessage());
            return response()->json(['title'=>'Error', 'msg' => 'Server error!', 'icon'=>'error'], 400);
        }
    }

    public function redeem(Request $request)
    {
        try {
            $coupon = Coupon::where('coupon', $request->coupon)->first();
            if (!$coupon) {
                return response()->json(['error' => 'Coupon not found!'], 400);
            }
            if ($coupon->user_id) {
                return response()->json(['error' => 'Coupon is already used!'], 400);
            }

            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->originator = '0';
            $user->level = 'OTO1';
            $user->pay_type = 0;
            $user->credit = 10;
            $user->integration = 0;
            $user->whitelabels = 0;
            $user->template_admin = 0;
            $user->show_tutorial = 1;
            $user->save();

            $plan = Plan::where('level', 'OTO1')->first();

            $user->notify(new UserCreated($user->email, $request->password, $plan->id));
            CommonUtils::addMailwizz($user->email, $user->name, '');

            $coupon->user_id = $user->id;
            $coupon->save();

            if ($token = Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                return $this->respondWithToken($token, true, 'statistics');
            }

            return response()->json(['msg' => 'Success'], 200);
        } catch (\Throwable $e) {
            Log::error('redeem user: ' . $e->getMessage());
            return response()->json(['error' => 'Redeem coupon error!'], 400);
        }
    }

    public function appsumo(Request $request)
    {
        try {
            $user = User::find($request->id);
            if (!$user) {
                Log::error('invalid action via appsumo-profile : ' . $request->id);
                return response()->json(['error' => 'Invalid action!'], 400);
            }

            $user->name = $request->firstName . ' ' . $request->lastName;
            $user->password = bcrypt($request->password);
            $user->save();

            $plan = Plan::where('level', $user->level)->first();

            $user->notify(new UserCreated($user->email, $request->password, $plan->id));
            CommonUtils::addMailwizz($user->email, $request->firstName, $request->lastName);

            if ($token = Auth::attempt(['email' => $user->email, 'password' => $request->password])) {
                return $this->respondWithToken($token, true, 'memberships');
            }

            return response()->json(['msg' => 'Success'], 200);
        } catch (\Throwable $e) {
            Log::error('appsumo user: ' . $e->getMessage());
            return response()->json(['error' => 'Updating profile error!'], 400);
        }
    }
}
