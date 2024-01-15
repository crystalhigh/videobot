<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Models\PasswordReset;
use App\Notifications\ForgotPassword;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

use App\Models\Plan;
use App\Utils\CommonUtils;

class ForgotPasswordController extends Controller
{
    //
    public function forgot(Request $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            if (!$user) {
                return 'User not found!';
            }
            $reset = PasswordReset::where('email', $user->email)->first();
            $hash = hash::make($user->email . date('Y-m-d H:i:s'));
            $hash = str_replace('/', '', $hash);
            if ($reset) {
                $reset->token = $hash;
                $reset->update();
            } else {
                $reset = new PasswordReset();
                $reset->email = $user->email;
                $reset->token = $hash;
                $reset->save();
            }
            $url = env('APP_URL') . '/password/reset/' . $hash;
            $user->notify(new ForgotPassword($url));
            return 'Reset password link sent to your email!';
        } catch (\Throwable $e) {
            Log::error('forgot password . ' . $e->getMessage());
            return 'Cannot reset password now';
        }
    }

    public function reset(Request $request)
    {
        $token = $request->token;
        $password = $request->password;
        try {
            $reset = PasswordReset::where('token', $token)->first();
            if (!$reset) {
                return 'Invalid token, you cannot reset your password!';
            }
            $user = User::where('email', $reset->email)->first();
            if (!$user) {
                return 'User is not exist. Please contact with support team!';
            }
            $user->password = bcrypt($password);
            $user->save();

            $reset->delete();
            $token = Auth::attempt(['email' => $user->email, 'password' => $request->password]);

            // caclulate s3
            $storage = 0;
            $originator = Auth::user()->originator;
            if (!$originator || ($originator && strval($originator) == '0')) {
                $used = 0;
                $used = CommonUtils::s3Size();
                $user = User::find(Auth::user()->id);
                $user->s3 = $used;
                $user->save();
                if (in_array(Auth::user()->level, array('TIER1', 'TIER2', 'TIER3'))) {
                    $plan = Plan::where('level', Auth::user()->level)->first();
                    $storage = $plan->storage - $used;
                }
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
                'user' => $user,
                's3' => $storage,
                'goto' => $res_user->role == 'admin' ? 'admin-payout' : 'statistics'
            ]);
        } catch (\Throwable $e) {
            Log::error('Reset password: ' . $e->getMessage());
            return 'Server error!';
        }
    }
}
