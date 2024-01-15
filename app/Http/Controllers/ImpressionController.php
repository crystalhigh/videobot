<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use App\Models\Impression;
use App\Models\Vidpop;
use App\Models\VidpopupMetrics;

class ImpressionController extends Controller
{
    //
    public function load(Request $request)
    {
        try {
            $vid = Vidpop::where('code', $request->code)->first();
            if (!$vid) {
                return response()->json(['error' => 'VidGen not found!'], 400);
            }
            $imp = new Impression();
            $imp->vidpop_id = $vid->id;
            $imp->user_id = $vid->user_id;
            $imp->save();

            $metrics_id = 0;
            if ($request->pv_id != 0 && $request->index == 1) {
                $metrics_id = VidpopupMetrics::insertGetId([
                    'pv_id' => $request->pv_id,
                    'status' => 'view',
                    'created_at' => date('Y-m-d h:i:s'),
                    'updated_at' => date('Y-m-d h:i:s')
                ]);
            }
            return response()->json(['metrics_id' => $metrics_id], 200);
        } catch (\Throwable $e) {
            Log::error('impression save: ' . $e->getMessage());
            return response()->json(['error' => 'Check error!'], 400);
        }
    }
}
