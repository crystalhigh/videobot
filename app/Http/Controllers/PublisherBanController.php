<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PublisherBan;
use App\Models\PublisherVidpopup;
use Illuminate\Support\Facades\Auth;

class PublisherBanController extends Controller
{
	//
	public function create(Request $request) {
		$advertiser_id = Auth::user()->id;
		$publisher_id = $request->publisher_id;
		$count = PublisherBan::where('advertiser_id', $advertiser_id)->where('publisher_id', $publisher_id)->count();
		if ($count == 0) {
			$publisherBan = new PublisherBan();
			$publisherBan->advertiser_id = $advertiser_id;
			$publisherBan->publisher_id = $publisher_id;
			$publisherBan->save();
			$pv_rows = PublisherVidpopup::where('creator_id', $advertiser_id)->where('publisher_id', $publisher_id)->get();
			foreach($pv_rows as $pv) {
				$pv->status = "Rejected";
				$pv->save();
			}
			$res = $publisherBan;

			return response()->json($res, 200);
		} else
			return response()->json(['error' => 'This publisher has already been banned!'], 200);
	}

	public function delete(Request $request, $id) {
		try {
			$row = PublisherBan::find($id);
			if (!$row)
				return response()->json(['error' => 'Banned publisher does not exist!'], 200);
			$pv_rows = PublisherVidpopup::where('creator_id', $row->advertiser_id)->where('publisher_id', $row->publisher_id)->get();
			foreach($pv_rows as $pv) {
				$pv->status = "Pending";
				$pv->save();
			}
			$row->delete();
			return response()->json([], 200);
		} catch (\Throwable $e) {
			Log::error('delete banned publisher: ' . $e->getMessage());
			return response()->json(['error' => 'Check error'], 400);
		}
	}
}
