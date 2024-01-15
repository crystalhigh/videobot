<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\PublisherVidpopup;
use App\User;

class PublisherVidpopupController extends Controller
{
    //
    public function get(Request $request)
		{
			
		}

		public function advertiserRequest(Request $request) {
			try {
				$user_id = Auth::user()->id;
				$res = PublisherVidpopup::where('creator_id', $user_id)->with('publisher')->with('vidgen')->with('publisherBan')->orderBy('updated_at', 'desc')->get();
				foreach($res as $r) {
					$r['revenue'] = PublisherVidpopup::select('publisher_id', 'vidpopup_id', 'cost')->join('vidpops', 'vidpops.id', '=', 'publisher_vidpopup.vidpopup_id')->join('vidpopup_metrics', 'vidpopup_metrics.pv_id', '=', 'publisher_vidpopup.id')->where('publisher_vidpopup.publisher_id', $r->publisher_id)->where('vidpopup_metrics.status', 'click')->get();
				}
				return response()->json($res, 200);
			} catch (\Throwable $e) {
				Log::error('load requests error: ' . $e->getMessage());
				return response()->json(['error' => 'Check error'], 400);
			}
		}
		
		public function publisherResponse(Request $request) {
			try {
				$user_id = Auth::user()->id;
				$res = PublisherVidpopup::where('publisher_id', $user_id)
					->with('advertiser')
					->with('vidgen')
					->with('steps')
					->orderBy('updated_at', 'desc')
					->get();
				return response()->json($res, 200);
			} catch (\Throwable $e) {
				Log::error('load responses error: ' . $e->getMessage());
				return response()->json(['error' => 'Check error'], 400);
			}
		}
		
		public function reqResCount(Request $request) {
			try {
				$user_id = Auth::user()->id;
				$res['advertiser'] = PublisherVidpopup::where('creator_id', $user_id)->where('status', 'Pending')->count();
				$res['publisher'] = PublisherVidpopup::where('publisher_id', $user_id)->where('status', '<>', 'Pending')->count();
				return response()->json($res, 200);
			} catch (\Throwable $e) {
				Log::error('load requests error: ' . $e->getMessage());
				return response()->json(['error' => 'Check error'], 400);
			}
		}
		
		public function transaction(Request $request) {
			$res = [];
			$user_id = Auth::user()->id;
			$start_date = $request->start;
			$end_date = $request->end;
			$currentPage = $request->page;
			$perPage = $request->perPage;
			$all = [];
			if (Auth::user()->role != 'publisher') {
				$tmp = PublisherVidpopup::with('vidgen:id,name,cost')->with('publisher:id,name')
					->withCount([
						'metrics as metrics_view_count'=> function($query) use ($start_date, $end_date) {
							$query->whereBetween('updated_at', [$start_date.' 00:00:00', $end_date.' 23:59:59']);
						},
						'metrics as metrics_click_count'=> function($query) use ($start_date, $end_date) {
							$query->whereBetween('updated_at', [$start_date.' 00:00:00', $end_date.' 23:59:59'])->where('status', 'click');
						},
						'metrics as metrics_paid_count'=> function($query) use ($start_date, $end_date) {
							$query->whereBetween('updated_at', [$start_date.' 00:00:00', $end_date.' 23:59:59'])->where('status', 'click')->where('paid_status', 'Paid');
						},
						'metrics as metrics_pending_count'=> function($query) use ($start_date, $end_date) {
							$query->whereBetween('updated_at', [$start_date.' 00:00:00', $end_date.' 23:59:59'])->where('status', 'click')->where('paid_status', 'Pending');
						},
					])
					->addSelect([
						'metrics_first_click_date' => function($query) {
							$query->select('updated_at')->from('vidpopup_metrics')->whereColumn('pv_id', 'publisher_vidpopup.id')->where('status', 'click')->orderBy('updated_at')->limit(1);
						},
						'metrics_last_click_date' => function($query) {
							$query->select('updated_at')->from('vidpopup_metrics')->whereColumn('pv_id', 'publisher_vidpopup.id')->where('status', 'click')->orderBy('updated_at', 'desc')->limit(1);
						},
					])
					->withCount(['impression' => function($query) use ($start_date, $end_date) {
						$query->whereBetween('updated_at', [$start_date.' 00:00:00', $end_date.' 23:59:59']);
					}])
					->where('creator_id', $user_id)->where('status', 'Approved')->having('metrics_click_count', '<>', 0)->orderBy('updated_at', 'desc')->paginate($perPage, ['*'], 'page', $currentPage);
			} else {
				$tmp = PublisherVidpopup::with('vidgen:id,name,cost')->with('advertiser:id,name')
					->withCount([
						'metrics as metrics_view_count'=> function($query) use ($start_date, $end_date) {
							$query->whereBetween('updated_at', [$start_date.' 00:00:00', $end_date.' 23:59:59']);
						},
						'metrics as metrics_click_count'=> function($query) use ($start_date, $end_date) {
							$query->whereBetween('updated_at', [$start_date.' 00:00:00', $end_date.' 23:59:59'])->where('status', 'click');
						},
						'metrics as metrics_paid_count'=> function($query) use ($start_date, $end_date) {
							$query->whereBetween('updated_at', [$start_date.' 00:00:00', $end_date.' 23:59:59'])->where('status', 'click')->where('paid_status', 'Paid');
						},
						'metrics as metrics_pending_count'=> function($query) use ($start_date, $end_date) {
							$query->whereBetween('updated_at', [$start_date.' 00:00:00', $end_date.' 23:59:59'])->where('status', 'click')->where('paid_status', 'Pending');
						},
					])
					->addSelect([
						'metrics_first_click_date' => function($query) {
							$query->select('updated_at')->from('vidpopup_metrics')->whereColumn('pv_id', 'publisher_vidpopup.id')->where('status', 'click')->orderBy('updated_at')->limit(1);
						},
						'metrics_last_click_date' => function($query) {
							$query->select('updated_at')->from('vidpopup_metrics')->whereColumn('pv_id', 'publisher_vidpopup.id')->where('status', 'click')->orderBy('updated_at', 'desc')->limit(1);
						},
					])
					->withCount(['impression' => function($query) use ($start_date, $end_date) {
						$query->whereBetween('updated_at', [$start_date.' 00:00:00', $end_date.' 23:59:59']);
					}])
					->where('publisher_id', $user_id)->where('status', 'Approved')->having('metrics_click_count', '<>', 0)->orderBy('updated_at', 'desc')->paginate($perPage, ['*'], 'page', $currentPage);
			}
			$res['transaction'] = $tmp;
			$res['balance'] = User::find($user_id)->balance;
			return response()->json($res, 200);
		}

		public function update(Request $request, $id) {
			try {
				$p_vidgen = PublisherVidpopup::find($id);
				if (!$p_vidgen) {
				return response()->json(['error' => 'Cannot find publisher vidgen right now!'], 400);
				}
				$status = $request->status;
				$p_vidgen->status = $status;
				$p_vidgen->save();
				$user_id = Auth::user()->id;
				$advRequestCount = PublisherVidpopup::where('creator_id', $user_id)->where('status', 'Pending')->count();
				return response()->json(['vidgen' => $p_vidgen, 'advRequestCount' => $advRequestCount], 200);
			} catch (\Throwable $e) {
				Log::error('update status error: ' . $e->getMessage());
				return response()->json(['error' => 'Cannot update status right now!'], 400);
			}
		}

		public function getPublishedStatus(Request $request) {
			$pv_id = $request->pv_id;
			$res = PublisherVidpopup::find($pv_id);
			return response()->json($res, 200);
		}
}
