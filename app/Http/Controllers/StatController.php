<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Impression;
use App\Models\Reply;
use App\Utils\CommonUtils;
use App\User;
use Aws\Command;

class StatController extends Controller
{
    //
    public function statImpressions(Request $request)
    {
        try {
            $userid = CommonUtils::getUserId();
            $start = $request->start . ' 00:00:00';
            $end = $request->end . ' 23:59:59';
            $vid = $request->vid;

            if ($vid == -1) {
                $impressions = Impression::where('created_at', '>', $start)->where('created_at', '<', $end)->where('user_id', $userid)->orderBy('created_at')->get();
            } else {
                $impressions = Impression::where('vidpop_id', $vid)->where('created_at', '>', $start)->where('created_at', '<', $end)->where('user_id', $userid)->orderBy('created_at')->get();
            }

            $series = [];
            $current = '';
            $i = -1;
            foreach ($impressions as $imp) {
                $formated = date('Y-m-d', strtotime($imp->created_at));
                if ($current == '' || $current != $formated) {
                    $series[] = [
                        'x' => $formated,
                        'y' => 1,
                    ];
                    $current = $formated;
                    $i++;
                } else {
                    $series[$i]['y'] += 1;
                }
            }
            $data = [
                'data' => $series,
            ];
            return response()->json(['data' => $data], 200);
        } catch (\Throwable $e) {
            Log::error('stat impression: ' . $e->getMessage());
            return response()->json(['error' => 'Server Error'], 400);
        }
    }

    public function statReplies(Request $request)
    {
        try {
            $userid = CommonUtils::getUserId();
            $start = $request->start . ' 00:00:00';
            $end = $request->end . ' 23:59:59';
            $vid = $request->vid;

            if ($vid == -1) {
                $replies = Reply::where('created_at', '>', $start)->where('created_at', '<', $end)->where('user_id', $userid)->orderBy('created_at')->get();
            } else {
                $replies = Reply::where('vidpop_id', $vid)->where('created_at', '>', $start)->where('created_at', '<', $end)->where('user_id', $userid)->orderBy('created_at')->get();
            }

            $series = [];
            $current = '';
            $i = -1;
            foreach ($replies as $rep) {
                $formated = date('Y-m-d', strtotime($rep->created_at));
                if ($current == '' || $current != $formated) {
                    $series[] = [
                        'x' => $formated,
                        'y' => 1,
                    ];
                    $current = $formated;
                    $i++;
                } else {
                    $series[$i]['y'] += 1;
                }
            }
            $data = [
                'data' => $series,
            ];
            return response()->json(['data' => $data], 200);
        } catch (\Throwable $e) {
            Log::error('stat replies: ' . $e->getMessage());
            return response()->json(['error' => 'Server Error'], 400);
        }
    }

    public function monthlyImpression()
    {
        try {
            $userid = CommonUtils::getUserId();
            $user = User::find($userid);
            $range = CommonUtils::getRangeDate($user->created_at);
            $start = $range[0];
            $end = $range[1];
            $impressions = Impression::where('created_at', '>', $start)->where('created_at', '<', $end)->where('user_id', $userid)->count();
            $limit = 0;
            if ($user->level == 'FET') {
                $limit = env('MAX_FET_IMPRESSION');
            } else if ($user->level == 'OTO1' || $user->level == 'TIER1' || $user->level == 'FE') {
                $limit = env('MAX_FE_IMPRESSION');
            } else {
                $limit = -1;
            }
            $limit = intVal($limit);
            $state = 'normal';
            if ($limit == -1) {
            }
            if ($limit <= $impressions && $limit != -1) {
                $state = 'over';
            }
            return response()->json(['count' => $impressions, 'limit' => $limit, 'state' => $state], 200);

        } catch (\Throwable $e) {
            Log::error('monthly Impression error: ' . $e->getMessage());
            return response()->json(['error' => 'Cannot get monthly impression!'], 400);
        }
    }
}
