<?php

namespace App\Http\Controllers;

use App\Models\Vidpop;
use App\Models\Step;
use App\Utils\CommonUtils;
use App\Models\Brand;
use App\User;
use App\Models\Impression;
use App\Models\PublisherVidpopup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class LiveController extends Controller
{
    //
    public function live(Request $request)
    {
        try {
            $code = $request->code;
            $vidpop = Vidpop::where('code', $code)->first();
            if ($vidpop) {
                // get user liimitation
                $user = User::find($vidpop->user_id);
                $range = CommonUtils::getRangeDate($user->created_at);
                $start = $range[0];
                $end = $range[1];
                $impressions = Impression::where('created_at', '>', $start)->where('created_at', '<', $end)->where('user_id', $vidpop->user_id)->count();
                $limit = 0;
                if ($vidpop->is_template == 0) {
                    if ($user->level == 'FET') {
                        $limit = env('MAX_FET_IMPRESSION');
                    } else if ($user->level  == 'FE' || $user->level == 'TIER1' || $user->level == 'OTO1') {
                        $limit = env('MAX_FE_IMPRESSION');
                    }
                    $limit = intVal($limit);
                    if ($user->level != 'TIER2' && $user->level != 'TIER3' && $user->level != 'OTO2' && $limit <= $impressions) {
                        return response()->json(['error' => 'limit'], 400);
                    }
                }

                $steps = Step::where('vidpop_id', $vidpop->id)->orderBy('index')->get();
                $decodedVid = CommonUtils::decodeVidpop($vidpop);
                $decodedSteps = [];
                foreach ($steps as $s) {
                    $decodedSteps[] = CommonUtils::decodeStep($s);
                }
                $color = '#ffffff';
                $bg_color = '#1998e4';
                $name = 'vid-gen.com';
                $url = 'https://vid-gen.com';
                if ($vidpop->brand != 'VidGen') {
                    $brand = Brand::find($vidpop->brand);
                    if ($brand) {
                        $color = $brand->color;
                        $bg_color = $brand->bg_color;
                        $name = $brand->name;
                        $url = $brand->url;
                    }
                }
                $publisher = json_decode('{}');
                if ($request->pv_id) {
                    $pv_id = $request->pv_id;
                    $publisher = PublisherVidpopup::where('id', $pv_id)->with('publisher')->first();
                }
                return response()->json(['vidpop' => $decodedVid, 'steps' => $decodedSteps, 'color' => $color, 'bg_color' => $bg_color, 'brand' => $name, 'url' => $url, 'publisher' => $publisher, 'advertiser' => $user], 200);
            } else {
                return response()->json(['error' => 'Not found!'], 400);
            }
        } catch (\Throwable $e) {
            Log::error('live error: ' . $e->getMessage());
            return response()->json(['error' => 'Not found!'], 400);
        }
    }

    public function view(Request $request)
    {
        $code = $request->code;
        $vidpop = Vidpop::where('code', $code)->first();
        if ($vidpop) {
            $decodedVid = CommonUtils::decodeVidpop($vidpop);
            if (!$decodedVid->social) {
                $meta = [];
                $meta['title'] = $vidpop->name ? $vidpop->name : 'VidGen Live';
                $meta['url'] = env('APP_URL') . '/live/' . $code;
                $meta['description'] = 'VidGen allows you to Interact with your website visitors in real-time and close more sales.';
                return view('live', ['meta' => $meta]);
            }

            $user = User::find($vidpop->user_id);

            if (!$user || $user->level === 'FET') {
                $meta = [];
                $meta['title'] = $vidpop->name ? $vidpop->name : 'VidGen Live';
                $meta['url'] = env('APP_URL') . '/live/' . $code;
                $meta['description'] = 'VidGen allows you to Interact with your website visitors in real-time and close more sales.';
                return view('live', ['meta' => $meta]);
            }

            $social = is_array($decodedVid->social) ? $decodedVid->social : (array)$decodedVid->social;

            $meta = [];
            $meta['title'] = $vidpop->name ? $vidpop->name : 'VidGen Live';
            $meta['url'] = env('APP_URL') . '/live/' . $code;
            $meta['fbid'] = $social['fb_id'] ? $social['fb_id'] : 'unset';
            $meta['gaid'] = $social['ga_id'] ? $social['ga_id'] : 'unset';
            $meta['gtm'] = $social['gtm'] ?  $social['gtm'] : 'unset';
            $meta['description'] = $social['description'];

            if ($social['seo'] == 0 || $social['seo'] == '0') {
                $meta['seo'] = 'unset';
            } else {
                $meta['seo'] = 'set';
            }
            return view('livepro', ['meta' => $meta]);
        } else {
            $meta = [];
            $meta['title'] = 'VidGen Live';
            $meta['url'] = env('APP_URL') . '/error';
            $meta['description'] = 'VidGen allows you to Interact with your website visitors in real-time and close more sales.';
            return view('live', ['meta' => $meta]);
        }
    }
}
