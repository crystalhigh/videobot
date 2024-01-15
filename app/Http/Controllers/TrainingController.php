<?php

namespace App\Http\Controllers;

use App\Models\Training;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TrainingController extends Controller
{
    public function load(Request $request)
    {
        try {
            $trainings = Training::orderBy('created_at')->get();
            return $trainings;
        } catch (\Throwable $e) {
            Log::error('Load training : ' . $e->getMessage());
        }
    }
}
