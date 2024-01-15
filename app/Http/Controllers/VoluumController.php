<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class VoluumController extends Controller
{
    //
    public function getConversionData(Request $request) {
        $resp = Http::withHeaders([
            'Content-Type' => 'application/json; charset=utf-8',
            'Accept' => 'application/json'
        ])->post('https://api.voluum.com/auth/access/session', [
            'accessId' => "e4bd6554-a0e4-4f29-a8f7-3ad87e0ec443",
            'accessKey' => "glolSIOpM5ebi0-A2702Rb2Eo_jGokwyRFuk"
        ]);
        
        $token = null;
        if ($resp->successful()) {
            $data = $resp->json();
            $token = $data['token'];
        } else {
            $statusCode = $resp->status();
        }
        $conversions = null;
        if ($token) {
            $data = [
                'workspaces' => 'bb1ee9a0-5e36-4d19-99a6-9f8c054fcdbd',
                'from' => ($request->from).'T00:00:00Z',
                'column' => ['campaignId', 'campaignName', 'revenue']
            ];
            if ($request->to)
                $data['to'] = ($request->to).'T00:00:00Z';
            $query = '';
            foreach($data as $key => $value) {
                if (is_array($value)) {
                    foreach($value as $k => $v) {
                        $query .= $key.'='.urlencode($v).'&';
                    }
                } else
                    $query .= $key.'='.urlencode($value).'&';
            }
            $query = rtrim($query, '&');
            Log::info($query);
            $resp = Http::withHeaders([
                'Content-Type' => 'application/json; charset=utf-8',
                'Accept' => 'application/json',
                'CWAUTH-TOKEN' => $token
            ])->get('https://api.voluum.com/report/conversions?'.$query);
            if ($resp->successful()) {
                $conversions = $resp->json();
                return response()->json($conversions);
            } else {
            }
        }
        return response()->json(['error' => 'Request error'], 400);
    }
}
