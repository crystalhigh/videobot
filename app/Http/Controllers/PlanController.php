<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\Plan;
use App\User;

use App\Notifications\UserCreated;
use App\Utils\CommonUtils;

class PlanController extends Controller
{
    //
    public function info(Request $request)
    {
        try {
            $info = Plan::where('index', $request->index)->first();
            if (!$info) {
                return response()->json(['error' => 'Plan not found!'], 400);
            }
            return response()->json(['plan' => $info], 200);
        } catch (\Throwable $e) {
            Log::error('plan error: ' . $e->getMessage());
            return response()->json(['error' => 'Server Error!'], 400);
        }
    }

    public function load()
    {
        try {
            $type = CommonUtils::userType();
            $plans = Plan::where('type', $type)->orderBy('index')->get();
            $res = [];
            foreach($plans as $p) {
                $tmp = $p;
                $tmp['features'] = preg_split("/\r\n|\n|\r/", $p->features);
                $res[] = $tmp;
            }
            return response()->json(['plans' => $res], 200);
        } catch (\Throwable $e) {
            Log::error('plan error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error!'], 400);
        }
    }

    public function test()
    {
        $user = User::find('0b7e42e0-b6c7-485b-bda6-3681159f708b ');
        $user->notify(new UserCreated('finniversen103@gmail.com', '123123', '2b4e715a-8dea-4ae7-b336-dee6f46a3323'));
        return 'Success';
    }
}
