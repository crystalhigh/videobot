<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Coupon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CouponController extends Controller
{
    //
    public function load()
    {
        $user = Auth::user();
        try {
            if (!$user->template_admin) {
                return response()->json(['error' => 'You have no permission to load coupon!!!'], 400);
            }
            $coupons = Coupon::all();
            return response()->json(['coupons' => $coupons]);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Cannot load coupon data now!'], 400);
        }
    }

    public function create(Request $request)
    {
        try {
            $coupons = explode(',', $request->coupons);
            $res = [];
            foreach($coupons as $c) {
                $coupon = new Coupon();
                $coupon->coupon = $c;
                $coupon->once = 0;
                $coupon->level = $request->level;
                $coupon->save();
                $res[] = $coupon;
            }
            return response()->json(['coupons' => $res], 200);
        } catch (\Throwable $e) {
            Log::error('create coupon error : ' . $e->getMessage());
            return response()->json(['error' => 'Cannot create coupon right now!'], 400);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $coupon = Coupon::find($id);
            if (!$coupon) {
                return response()->json(['error' => 'Cannot find coupon right now!'], 400);
            }
            $coupon->coupon = $request->coupon;
            $coupon->level = $request->level;
            $coupon->save();
            return response()->json(['coupon' => $coupon], 200);
        } catch (\Throwable $e) {
            Log::error('update coupon error: ' . $e->getMessage());
            return response()->json(['error' => 'Cannot update coupon right now!'], 400);
        }
    }

    public function delete($id)
    {
        try {
            $coupon = Coupon::find($id);
            if (!$coupon) {
                return response()->json(['error' => 'Cannot find coupon right now!'], 400);
            }
            $coupon->delete();
            return response()->json([], 204);
        } catch (\Throwable $e) {
            Log::error('delete coupon error: ' . $e->getMessage());
            return response()->json(['error' => 'Cannot delete coupon right now!'], 400);
        }
    }
}
