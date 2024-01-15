<?php

namespace App\Http\Controllers;

use App\User;
use App\Utils\CommonUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use App\Notifications\UserCreated;
use App\Models\Coupon;
use App\Models\Plan;
use Aws\Command;

class UserController extends Controller
{
		public function getAdvertisers() {
			try {
					$users = User::where('role', 'advertiser')->orWhere('role', 'origin')->get();
					return response()->json($users, 200);
			} catch (\Throwable $e) {
					Log::error('Advertiser load : ' . $e->getMessage());
					return response()->json(['error' => 'Server error!'], 400);
			}
		}

    public function subusers()
    {
        try {
            $users = User::where('originator', Auth::user()->id)->get();
            return response()->json($users, 200);
        } catch (\Throwable $e) {
            Log::error('Member load : ' . $e->getMessage());
            return response()->json(['error' => 'Server error!'], 400);
        }
    }

    public function save(Request $request)
    {
        try {
            $user = User::find(Auth::user()->id);
            if (!$user) {
                return response()->json(['error' => 'User not found!'], 400);
            }
            $user->name = $request->name;
            $user->email = $request->email;
            if (isset($request->avatar)) {
                $user->avatar = $request->avatar;
            }
            if (isset($request->country)) {
                $user->country = $request->country;
            }
            if (isset($request->city)) {
                $user->city = $request->city;
            }
            if (isset($request->state)) {
                $user->state = $request->state;
            }
            if (isset($request->zip_code)) {
                $user->zip_code = $request->zip_code;
            }
            $user->deep_api = $request->deep_api;
            $user->deep_secret = $request->deep_secret;
            $user->save();
            return response()->json($user, 200);
        } catch (\Throwable $e) {
            Log::error('user save: ' . $e->getMessage());
            return response()->json(['error' => 'Server Error'], 400);
        }
    }

    public function uploadAvatar()
    {
        try {
            if (!empty($_FILES['file']['tmp_name'])) {
                if (is_uploaded_file($_FILES['file']['tmp_name'])) {
                    $_file_type = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                }
                $name_original = md5(time() . rand());
                $image_name = $name_original . '.' . $_file_type;

                $prefix = 'uploads/users/' . Auth::user()->id;

                $filePath = storage_path('app/public/' . $prefix . '/' . $image_name);

                if (!file_exists(storage_path('app/public/uploads'))) {
                    mkdir(storage_path('app/public/uploads'));
                }

                if (!file_exists(storage_path('app/public/' . $prefix))) {
                    mkdir(storage_path('app/public/' . $prefix));
                }
                if (move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
                    @chmod($filePath, 0755);
                }

                if (!Storage::disk('s3')->exists($prefix)) {
                    Storage::disk('s3')->makeDirectory($prefix);
                }

                $fileContent = Storage::get($prefix . '/' . $image_name);
                Storage::disk('s3')->put($prefix . '/' . $image_name, $fileContent, [
                    'ACL' => 'public-read-write'
                ]);

                Storage::delete($prefix . '/' . $image_name);

                return response()->json($prefix . '/' . $image_name, 200);
            }
            return response()->json(['error' => 'File is not set'], 400);
        } catch (\Throwable $e) {
            Log::error('uploadImage: ' . $e->getMessage());
            return response()->json(['error' => 'Server Error!'], 400);
        }
    }

    public function updateUser(Request $request)
    {
        // this is for sub-user
        try {
            $id = $request->id;
            $name = $request->name;
            $email = $request->email;

            if ($id === '' || !$id) {
                $u = User::where('email', $email)->first();
                if ($u) {
                    return response()->json(['error' => 'This email exists already!'], 400);
                }
                $originator = User::find(Auth::user()->id);
                $count = User::where('originator', $originator->id)->count();
                if ($count === $originator->whitelabels) {
                    return response()->json(['error' => 'You are now limited to create user.'], 400);
                }
                $user = new User();
                $pwd = CommonUtils::randomPassword(8);
                $user->password = bcrypt($pwd);
                $user->originator = $originator->id;
                $user->show_tutorial = 1;
            } else {
                $user = User::find($id);
                $pwd = '';
            }
            $user->name = $name;
            $user->email = $email;
            $user->level = 'FE';
            $user->pay_type = 0;
            $user->credit = 10;
            $user->integration = 0;
            $user->whitelabels = 0;
            $user->template_admin = 0;
            $user->save();
            if ($id === '' || !$id) {
                $plan = Plan::where('level', 'FE')->first();
                $user->notify(new UserCreated($user->email, $pwd, $plan->id, true));
                $res = CommonUtils::addMailwizz($email, $name, '');
            }
            return response()->json(['user' => $user, 'pwd' => $pwd], 200);
        } catch (\Throwable $e) {
            Log::error('update user: ' . $e->getMessage());
            return response()->json(['error' => 'Server Error!'], 400);
        }
    }

    public function verifyCredit($id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['error' => 'User not found!'], 400);
            }
            $user->credit_info = "Checked";
            $user->save();
            return response()->json(['user' => $user], 200);
        } catch (\Throwable $e) {
            Log::error('verify Credit: ' . $e->getMessage());
            return response()->json(['error' => 'Server Error!'], 400);
        }
    }

    public function deleteUser($id)
    {
        try {
            $user = User::where('id', $id)->where('originator', Auth::user()->id)->first();
            if (!$user) {
                return response()->json(['error' => 'User not found!'], 400);
            }
            $user->delete();
            return response()->json('success', 204);
        } catch (\Throwable $e) {
            Log::error('delete user: ' . $e->getMessage());
            return response()->json(['error' => 'Server error!'], 400);
        }
    }

    public function cancelSubscribe()
    {
        try {
            $vendor_id = env('VENDOR_ID');
            $auth_code = env('VENDOR_AUTH_CODE');
            $req = new Client();
            $user = User::find(Auth::user()->id);
            if (!$user) {
                return response()->json(['error' => 'User not found!'], 400);
            }
            // check the current level
            if ($user->level === 'FET' || $user->level === 'OTO1') {
                return response()->json('success', 200);
            }
            $cancel_url = env('PADDLE_URL') . '/users_cancel';
            if ($user->subscribe && $user->cancel_url) {
                $res = $req->request('POST', $cancel_url, [
                    'json' => [
                        'vendor_id' => $vendor_id,
                        'vendor_auth_code' => $auth_code,
                        'subscription_id' => $user->subscribe
                    ]
                ]);
                if ((int)$res->getStatusCode() == 200) {
                    // deactivate user
                    $user->level = 'FET';
                    $user->save();
                }
            } else {
                $user->level = 'FET';
                $user->save();
            }
            return response()->json('success', 200);
        } catch (\Throwable $e) {
            Log::error('cancel subscribe: ' . $e->getMessage());
            return response()->json(['error' => 'Server error!'], 400);
        }
    }

    public function updateSubscribe(Request $request)
    {
        try {
            $user = User::find(Auth::user()->id);
            $user->level = $request->level;
            if ($request->subscription_id) {
                $user->subscribe = $request->subscription_id;
            }
            if ($request->cancel_url) {
                $user->cancel_url = $request->cancel_url;
            }
            $user->save();
            return response()->json(['user' => $user], 200);
        } catch (\Throwable $e) {
            Log::error('update subscribe: ' . $e->getMessage());
            return response()->json(['error' => 'Server error!'], 400);
        }
    }

    public function updateTutorial()
    {
        try {
            $user = User::find(Auth::user()->id);
            $user->show_tutorial = 0;
            $user->save();
            return response()->json('Done', 200);
        } catch (\Throwable $e) {
            Log::error('update show Tutorial: ' . $e->getMessage());
            return response()->json(['error' => 'Server error!'], 400);
        }
    }

    public function storageStatus()
    {
        try {
            $size = CommonUtils::s3Size();
            return response()->json(['size' => $size], 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Server error!'], 400);
        }
    }
}
