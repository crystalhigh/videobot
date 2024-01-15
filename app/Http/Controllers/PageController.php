<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

use App\Models\Page;

class PageController extends Controller
{
    //
    public function load(Request $request)
    {
        try {
            $label = $request->label;
            $page = Page::where('label', $label)->first();
            if ($page) {
                return response()->json(['page' => $page->content], 200);
            } else {
                return response()->json(['error' => 'Page not found'], 400);
            }
        } catch (\Throwable $e) {
            Log::error('Page load error : ' . $e->getMessage());
            return response()->json(['error' => 'Server error!'], 400);
        }
    }
}
