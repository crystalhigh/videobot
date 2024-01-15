<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VidpopupMetrics;
use App\Models\PublisherVidpopup;
use App\Models\PublisherBan;
use App\Models\Vidpop;
use App\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class VidpopMetricsController extends Controller
{
	//
	public function get(Request $request)
	{
		try {
			$start_date = $request->start;
			$end_date = $request->end;
			$v_id = $request->vid;
			$role = Auth::user()->role;
			$user_id = Auth::user()->id;
			if ($role && $role == 'publisher') {
				$rows = PublisherVidpopup::select('publisher_id', 'vidpopup_id', 'name', DB::raw('count(vidpopup_id) as temp'))->join('vidpops', 'vidpops.id', '=', 'publisher_vidpopup.vidpopup_id')->where('publisher_vidpopup.publisher_id', $user_id)->where('publisher_vidpopup.status', 'Approved')->groupBy('publisher_vidpopup.publisher_id', 'publisher_vidpopup.vidpopup_id', 'vidpops.name');
				// if ($v_id != -1)
				//     $rows = $rows->where('publisher_vidpopup.vidpopup_id', $v_id);
				$res['all_vidpops'] = $rows->get();

				$rows = PublisherVidpopup::where('publisher_id', $user_id)->where('status', 'Approved');
				// if ($v_id != -1)
				//     $rows = $rows->where('vidpopup_id', $v_id);
				$res['total_campaigns'] = $rows->get();

				$rows = VidpopupMetrics::select('publisher_id', 'vidpopup_id', 'pv_id')->join('publisher_vidpopup', 'publisher_vidpopup.id', '=', 'vidpopup_metrics.pv_id')->whereBetween('vidpopup_metrics.updated_at', [$start_date.' 00:00:00', $end_date.' 23:59:59'])->where('publisher_vidpopup.publisher_id', $user_id)->where('publisher_vidpopup.status', 'Approved');
				if ($v_id != -1)
						$rows = $rows->where('publisher_vidpopup.vidpopup_id', $v_id);
				$res['video_plays'] = $rows->get();

				$rows = VidpopupMetrics::join('publisher_vidpopup', 'publisher_vidpopup.id', '=', 'vidpopup_metrics.pv_id')->whereBetween('vidpopup_metrics.updated_at', [$start_date.' 00:00:00', $end_date.' 23:59:59'])->where('publisher_vidpopup.publisher_id', $user_id)->where('publisher_vidpopup.status', 'Approved')->where('vidpopup_metrics.status', 'click');
				if ($v_id != -1)
						$rows = $rows->where('publisher_vidpopup.vidpopup_id', $v_id);
				$res['leads'] = $rows->get();

				// Counts of watched vidpops by users that you published.
				$rows = VidpopupMetrics::select('publisher_id', 'vidpopup_id', DB::raw('count(vidpopup_id) as temp'))->join('publisher_vidpopup', 'publisher_vidpopup.id', '=', 'vidpopup_metrics.pv_id')->whereBetween('vidpopup_metrics.updated_at', [$start_date.' 00:00:00', $end_date.' 23:59:59'])->where('publisher_vidpopup.publisher_id', $user_id)->where('publisher_vidpopup.status', 'Approved')->groupBy('publisher_vidpopup.vidpopup_id', 'publisher_vidpopup.publisher_id');
				if ($v_id != -1)
						$rows = $rows->where('publisher_vidpopup.vidpopup_id', $v_id);
				$res['views'] = $rows->get();

				// $rows = PublisherVidpopup::select('publisher_id', 'vidpopup_id', 'cost')->join('vidpops', 'vidpops.id', '=', 'publisher_vidpopup.vidpopup_id')->join('vidpopup_metrics', 'vidpopup_metrics.pv_id', '=', 'publisher_vidpopup.id')->whereBetween('publisher_vidpopup.updated_at', [$start_date.' 00:00:00', $end_date.' 23:59:59'])->where('publisher_vidpopup.publisher_id', $user_id)->where('publisher_vidpopup.status', 'Approved')->where('vidpopup_metrics.status', 'click')->where('vidpopup_metrics.paid_status', 'Paid');

				// 	$rows = PublisherVidpopup::select('publisher_id', 'vidpopup_id', 'cost')->join('vidpops', 'vidpops.id', '=', 'publisher_vidpopup.vidpopup_id')->join('vidpopup_metrics', 'vidpopup_metrics.pv_id', '=', 'publisher_vidpopup.id')->whereBetween('publisher_vidpopup.updated_at', [$start_date.' 00:00:00', $end_date.' 23:59:59'])->where('publisher_vidpopup.publisher_id', $user_id)->where('publisher_vidpopup.status', 'Approved')->where('vidpopup_metrics.status', 'click');
				//   if ($v_id != -1)
				//       $rows = $rows->where('publisher_vidpopup.vidpopup_id', $v_id);
				//   $res['revenue'] = $rows->get();

				$rows = VidpopupMetrics::where('vidpopup_metrics.status', 'click')->join('publisher_vidpopup', 'vidpopup_metrics.pv_id', '=', 'publisher_vidpopup.id')->join('vidpops', 'vidpops.id', '=', 'publisher_vidpopup.vidpopup_id')->whereBetween('vidpopup_metrics.updated_at', [$start_date.' 00:00:00', $end_date.' 23:59:59'])->where('publisher_vidpopup.publisher_id', $user_id)->where('publisher_vidpopup.status', 'Approved');
				if ($v_id != -1)
					$rows = $rows->where('publisher_vidpopup.vidpopup_id', $v_id);
				$res['revenue'] = $rows->get();

				$rows = PublisherVidpopup::select(DB::raw('date(created_at) as created_at'), DB::raw('count(created_at) as published_count'), 'vidpopup_id')->where('publisher_id', $user_id)->where('status', 'Approved')->whereBetween('created_at', [$start_date.' 00:00:00', $end_date.' 23:59:59'])->groupBy(DB::raw('date(created_at)'), 'vidpopup_id')->orderBy('created_at');
				if ($v_id != -1)
						$rows = $rows->where('vidpopup_id', $v_id);
				$res['chart'] = $rows->get();

				$rows = PublisherVidpopup::with('vidgen:id,name,cost')->with('advertiser:id,name')
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
					->where('publisher_id', $user_id)->where('status', 'Approved')->having('metrics_click_count', '<>', 0)->orderBy('updated_at', 'desc')->get();
				if ($v_id != -1)
					$rows = $rows->where('vidgen.id', $v_id);
				$res['table_calc'] = $rows->values()->toArray();
			} else if ($role && ($role == 'advertiser' || $role == 'origin')) {
				$rows = Vidpop::where('user_id', $user_id);
				// if ($v_id != -1)
				//     $rows = $rows->where('id', $v_id);
				$res['all_vidpops'] = $rows->get();

				$rows = PublisherVidpopup::where('creator_id', $user_id)->where('status', 'Approved');
				$res['total_campaigns'] = $rows->get();

				$rows = VidpopupMetrics::join('publisher_vidpopup', 'publisher_vidpopup.id', '=', 'vidpopup_metrics.pv_id')->whereBetween('vidpopup_metrics.updated_at', [$start_date.' 00:00:00', $end_date.' 23:59:59'])->where('publisher_vidpopup.creator_id', $user_id)->where('publisher_vidpopup.status', 'Approved')->where('vidpopup_metrics.status', 'click');
				if ($v_id != -1)
						$rows = $rows->where('publisher_vidpopup.vidpopup_id', $v_id);
				$res['plays_leads'] = $rows->get();

				$rows = VidpopupMetrics::select(DB::raw('count(pv_id) as plays'))->join('publisher_vidpopup', 'publisher_vidpopup.id', '=', 'vidpopup_metrics.pv_id')->whereBetween('vidpopup_metrics.updated_at', [$start_date.' 00:00:00', $end_date.' 23:59:59'])->where('publisher_vidpopup.creator_id', $user_id)->where('publisher_vidpopup.status', 'Approved')->groupBy('pv_id');
				if ($v_id != -1)
						$rows = $rows->where('publisher_vidpopup.vidpopup_id', $v_id);
				$res['plays_vidpopup_count'] = $rows->get();

				$rows = PublisherVidpopup::select('publisher_id', DB::raw('count(publisher_id) as temp'))->where('creator_id', $user_id)->groupBy('publisher_id');
				// if ($v_id != -1)
				//     $rows = $rows->where('publisher_vidpopup.vidpopup_id', $v_id);
				$res['approved_publishers'] = $rows->get();
				$res['banned_publishers'] = PublisherBan::where('advertiser_id', $user_id)->count();

				$rows = PublisherVidpopup::select('creator_id', 'vidpopup_id', 'cost')->join('vidpops', 'vidpops.id', '=', 'publisher_vidpopup.vidpopup_id')->join('vidpopup_metrics', 'vidpopup_metrics.pv_id', '=', 'publisher_vidpopup.id')->whereBetween('vidpopup_metrics.updated_at', [$start_date.' 00:00:00', $end_date.' 23:59:59'])->where('publisher_vidpopup.creator_id', $user_id)->where('vidpopup_metrics.status', 'click');
				if ($v_id != -1)
					$rows = $rows->where('publisher_vidpopup.vidpopup_id', $v_id);
				$res['spend'] = $rows->get();

				$rows = VidpopupMetrics::select('publisher_vidpopup.creator_id', 'vidpopup_id', DB::raw('count(vidpopup_id) as temp'))->join('publisher_vidpopup', 'publisher_vidpopup.id', '=', 'vidpopup_metrics.pv_id')->whereBetween('vidpopup_metrics.updated_at', [$start_date.' 00:00:00', $end_date.' 23:59:59'])->where('publisher_vidpopup.creator_id', $user_id)->where('publisher_vidpopup.status', 'Approved')->groupBy('publisher_vidpopup.vidpopup_id', 'publisher_vidpopup.creator_id');
				if ($v_id != -1)
						$rows = $rows->where('publisher_vidpopup.vidpopup_id', $v_id);
				$res['views'] = $rows->get();

				$rows = PublisherVidpopup::with('vidgen:id,name,cost')->with('publisher:id,name')
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
					->where('creator_id', $user_id)->where('status', 'Approved')->having('metrics_click_count', '<>', 0)->orderBy('updated_at', 'desc')->get();
				
				$res['table_calc'] = $rows->values()->toArray();

				$rows = PublisherVidpopup::select(DB::raw('date(created_at) as created_at'), DB::raw('count(created_at) as published_count'), 'vidpopup_id')->where('creator_id', $user_id)->where('status', 'Approved')->whereBetween('created_at', [$start_date.' 00:00:00', $end_date.' 23:59:59'])->groupBy(DB::raw('date(created_at)'), 'vidpopup_id')->orderBy('created_at');
				if ($v_id != -1)
						$rows = $rows->where('vidpopup_id', $v_id);
				$res['chart'] = $rows->get();
				$res['balance'] = User::find($user_id)->balance;
			} else if ($role && $role == 'agent') {
				$res['all_vidpops'] = Vidpop::get();

				$rows = PublisherVidpopup::where('status', 'Approved');
				$res['total_campaigns'] = $rows->get();

				$rows = VidpopupMetrics::join('publisher_vidpopup', 'publisher_vidpopup.id', '=', 'vidpopup_metrics.pv_id')->whereBetween('vidpopup_metrics.updated_at', [$start_date.' 00:00:00', $end_date.' 23:59:59'])->where('publisher_vidpopup.status', 'Approved')->where('vidpopup_metrics.status', 'click');
				if ($v_id != -1)
						$rows = $rows->where('publisher_vidpopup.vidpopup_id', $v_id);
				$res['plays_leads'] = $rows->get();

				$rows = VidpopupMetrics::select(DB::raw('count(pv_id) as plays'))->join('publisher_vidpopup', 'publisher_vidpopup.id', '=', 'vidpopup_metrics.pv_id')->whereBetween('vidpopup_metrics.updated_at', [$start_date.' 00:00:00', $end_date.' 23:59:59'])->where('publisher_vidpopup.status', 'Approved')->groupBy('pv_id');
				if ($v_id != -1)
						$rows = $rows->where('publisher_vidpopup.vidpopup_id', $v_id);
				$res['plays_vidpopup_count'] = $rows->get();

				$res['approved_publishers'] = User::where('role', 'publisher')->get();
				$res['banned_publishers'] = PublisherBan::select('publisher_id', DB::raw('count(publisher_id) as temp'))->groupBy('publisher_id')->count();

				$rows = VidpopupMetrics::select('publisher_vidpopup.creator_id', 'vidpopup_id', DB::raw('count(vidpopup_id) as temp'))->join('publisher_vidpopup', 'publisher_vidpopup.id', '=', 'vidpopup_metrics.pv_id')->whereBetween('vidpopup_metrics.updated_at', [$start_date.' 00:00:00', $end_date.' 23:59:59'])->where('publisher_vidpopup.status', 'Approved')->groupBy('publisher_vidpopup.vidpopup_id', 'publisher_vidpopup.creator_id');
				if ($v_id != -1)
						$rows = $rows->where('publisher_vidpopup.vidpopup_id', $v_id);
				$res['views'] = $rows->get();

				$rows = PublisherVidpopup::with('vidgen:id,name,cost')->with('publisher:id,name')
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
					->where('status', 'Approved')->having('metrics_click_count', '<>', 0)->orderBy('updated_at', 'desc')->get();
				
				$res['table_calc'] = $rows->values()->toArray();

				$rows = PublisherVidpopup::select(DB::raw('date(created_at) as created_at'), DB::raw('count(created_at) as published_count'), 'vidpopup_id')->where('status', 'Approved')->whereBetween('created_at', [$start_date.' 00:00:00', $end_date.' 23:59:59'])->groupBy(DB::raw('date(created_at)'), 'vidpopup_id')->orderBy('created_at');
				if ($v_id != -1)
						$rows = $rows->where('vidpopup_id', $v_id);
				$res['chart'] = $rows->get();
				$res['balance'] = User::find($user_id)->balance;
			}
			return response()->json($res, 200);
		} catch (\Throwable $e) {
			Log::error('vidpop all : ' . $e->getMessage());
			return response()->json(['error' => 'Check error'], 400);
		}
	}

	public function create(Request $request)
	{
		$metrics_id = VidpopupMetrics::insertGetId([
			'pv_id' => $request->pv_id,
			'status' => 'view',
			'created_at' => date('Y-m-d h:i:s'),
			'updated_at' => date('Y-m-d h:i:s')
		]);
		return response()->json(['metrics_id' => $metrics_id], 200);
	}

	public function update(Request $request, $id)
	{
		try {
			$metrics = VidpopupMetrics::find($id);
			if (!$metrics) {
				return response()->json(['error' => 'Cannot find metrics right now!'], 400);
			}
			$metrics->status = "click";
			$metrics->save();

			// $this->controlBalance($metrics->pv_id);
			return response()->json(['metrics' => $metrics], 200);
		} catch (\Throwable $e) {
			Log::error('update VidGen metrics error: ' . $e->getMessage());
			return response()->json(['error' => 'Cannot update VidGen metrics right now!'], 400);
		}
	}

	public function controlBalance($id)
	{
		$pv = PublisherVidpopup::find($id);
		if (!$pv)
				return response()->json(['error' => 'Cannot find publisher_vidpopup right now!'], 400);
		$vidpop = Vidpop::find($pv->vidpopup_id);
		if (!$vidpop)
				return response()->json(['error' => 'Cannot find vidpops right now!'], 400);
		$user_advertiser = User::find($pv->creator_id);
		$user_publisher = User::find($pv->publisher_id);
		if (!$user_advertiser || !$user_publisher)
				return response()->json(['error' => 'Cannot find user right now!'], 400);
		$user_advertiser->balance -= $vidpop->cost;
		$user_advertiser->save();
		$user_publisher->balance += $vidpop->cost;
		$user_publisher->save();
	}

	public function getClickCount(Request $request) {
		$pv_id = $request->pv_id;
		$pv = PublisherVidpopup::where('id', $pv_id)->with('vidgen')->with('advertiser')->first();
		if ($pv->advertiser['balance'] < $pv->vidgen['cost']) {
			$pv->visible = 0;
			$pv->save();
		} else {
			$pv->visible = 1;
			$pv->save();
		}
		$res['count'] = VidpopupMetrics::where('pv_id', $pv_id)->where('status', 'click')->count();
		$res['visible'] = $pv->visible;
		$res['status'] = $pv->status;
		return response()->json($res, 200);
	}

	public function delete($id)
	{
	}
}
