<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\Soundtrack;

class SoundtrackController extends Controller
{
    //
    public function load()
    {
        try {
            $soundtracks = Soundtrack::all();
            return response()->json(['soundtracks' => $soundtracks], 200);
        } catch (\Throwable $e) {
            Log::error('load soundtrack: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 400);
        }
    }
}
