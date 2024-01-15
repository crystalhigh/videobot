<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\PublisherVidpopup;
use App\Models\PublisherPayout;
use App\Models\VidpopupMetrics;
use App\User;
use Illuminate\Support\Facades\Log;

class PublisherPayoutController extends Controller
{
	//
	public function index(Request $request) {
	}

	public function getPayoutDistribution(Request $request) {
		$user_id = Auth::user()->id;
		$res = PublisherPayout::where('publisher_id', $user_id)->where('status', 'Pending')->orderBy('updated_at', 'desc')->get();
		return response()->json($res, 200);
	}

	public function create(Request $request) {
		$user_id = Auth::user()->id;
		$user = User::find($user_id);

		$withdraw = $request->withdraw;
		$payout = new PublisherPayout();
		$payout->withdraw = $withdraw;
		$payout->publisher_id = $user_id;
		$payout->remain = $user->balance - $withdraw;
		$payout->save();
		
		$user->balance -= $withdraw;
		$user->save();
		$res['payout'] = $payout;
		$res['balance'] = $user->balance;

		$currentDate = date('Y-m-d');
		$prevMonth = date('Y-m-d 23:59:59', strtotime('-1 month', strtotime($currentDate)));

		$pv_rows = PublisherVidpopup::where('publisher_id', $user_id)->with(['metrics' => function($query) use($prevMonth) {
			$query->where('status', 'click')->where('paid_status', 'Pending')->where('updated_at', '<', $prevMonth);
		}])->get();
		$mids = array();
		foreach($pv_rows as $pv) {
			foreach($pv->metrics as $m) {
				array_push($mids, $m->id);
			}
		}
		VidpopupMetrics::whereIn('id', $mids)->update(['paid_status' => 'Paid']);

		$money = PublisherVidpopup::where('publisher_id', $user_id)->with('vidgen:id,cost')->withCount([
			'metrics as metrics_pending_count' => function ($query) use ($prevMonth) {
				$query->where('status', 'click')->where('paid_status', 'Pending')->where('updated_at', '<', $prevMonth);
			},
		])->get();
		$available = 0;
		foreach($money as $m) {
			$available += $m->vidgen->cost * $m->metrics_pending_count;
		}
		$remain = 0;
		// $pp = PublisherPayout::where('publisher_id', $user_id)->where('updated_at', '<', $prevMonth)->orderBy('updated_at', 'desc')->orderBy('id', 'desc')->first();
		$pp = PublisherPayout::where('publisher_id', $user_id)->orderBy('updated_at', 'desc')->orderBy('id', 'desc')->first();
		if ($pp)
			$remain = $pp->remain;
		$res['available'] = $available + $remain;
		return response()->json($res, 200);
	}

	public function payout(Request $request) {
		$currentDate = date('Y-m-d');
		$prevMonth = date('Y-m-d 23:59:59', strtotime('-1 month', strtotime($currentDate)));
		
		$user_id = Auth::user()->id;
		$money = PublisherVidpopup::where('publisher_id', $user_id)->with('vidgen:id,cost')->withCount([
			'metrics as metrics_earned_count' => function ($query) {
				$query->where('status', 'click');
			},
			'metrics as metrics_pending_count' => function ($query) use ($prevMonth) {
				$query->where('status', 'click')->where('paid_status', 'Pending')->where('updated_at', '<', $prevMonth);
			}
		])->get();
		$earned = 0;
		$available = 0;
		foreach($money as $m) {
			$earned += $m->vidgen->cost * $m->metrics_earned_count;
			$available += $m->vidgen->cost * $m->metrics_pending_count;
		}
		$paid = 0;
		$payout = PublisherPayout::where('publisher_id', $user_id)->where('status', 'Approved')->get();
		foreach($payout as $p) {
			$paid += $p->withdraw;
		}
		$res['earned'] = $earned;
		$res['paid'] = $paid;
		$res['balance'] = User::find($user_id)->balance;
		
		$remain = 0;
		// $pp = PublisherPayout::where('publisher_id', $user_id)->where('updated_at', '<', $prevMonth)->orderBy('updated_at', 'desc')->orderBy('id', 'desc')->first();
		$pp = PublisherPayout::where('publisher_id', $user_id)->orderBy('updated_at', 'desc')->orderBy('id', 'desc')->first();
		if ($pp)
			$remain = $pp->remain;
		$res['available'] = $available + $remain;

		$last = PublisherPayout::where('status', 'Pending')->orderBy('created_at', 'desc')->first();
		$res['last_distribution'] = $last;
		return response()->json($res, 200);
	}

	public function adminPayout(Request $request) {
		$currentDate = date('Y-m-d');
		$prevMonth = date('Y-m-d 23:59:59', strtotime('-1 month', strtotime($currentDate)));
		$publisher_list = User::select('id', 'name', 'email', 'balance')->where('role', 'publisher')->with(['publisherVidpopup' => function($query) use($prevMonth) {
			$query->where('status', 'Approved')->with('vidgen:id,cost');
			$query->where('status', 'Approved')->withCount([
				'metrics as metrics_view_count',
				'metrics as metrics_click_count' => function($query1) {
					$query1->where('status', 'click');
				},
				'metrics as metrics_pending_count' => function($query1) use($prevMonth) {
					$query1->where('status', 'click')->where('paid_status', 'Pending')->where('updated_at', '<', $prevMonth);
				},
			]);
		}])
		->with(['publisherPayout' => function($query) {
			$query->orderBy('updated_at', 'desc')->orderBy('id', 'desc');
		}])
		->withCount(['publisherPayout as publisher_payout_count' => function($query) {
			$query->where('status', 'Pending');
		}])
		// ->get();
		->having('publisher_payout_count', '<>', 0)->get();

		$advertiser_list = User::select('id', 'name', 'email', 'balance')->where(function($query) {
			$query->where('role', 'advertiser')->orWhere('role', 'origin');
		})->with(['advertiserVidpopup' => function($query) {
			$query->where('status', 'Approved')->with('vidgen:id,name,cost');
			$query->where('status', 'Approved')->withCount([
				'metrics as metrics_view_count',
				'metrics as metrics_click_count' => function($query1) {
					$query1->where('status', 'click');
				},
				'metrics as metrics_pending_count' => function($query1) {
					$query1->where('status', 'click')->where('paid_status', 'Pending');
				},
				'metrics as metrics_paid_count' => function($query1) {
					$query1->where('status', 'click')->where('paid_status', 'Paid');
				},
			]);
		}])->get();
		$res['publisher'] = $publisher_list;
		$res['advertiser'] = $advertiser_list;
		return response()->json($res, 200);
	}

	public function adminAdvertiserTopUp(Request $request) {
		$advertiser_id = $request->advertiser_id;
		$amount = $request->amount;
		$row = User::find($advertiser_id);
		$row->balance += $amount;
		$row->save();
		return response()->json($row, 200);
	}

	public function adminPublisherWithdraw(Request $request) {
		$currentDate = date('Y-m-d');
		$prevMonth = date('Y-m-d 23:59:59', strtotime('-1 month', strtotime($currentDate)));

		$publisher_id = $request->publisher_id;
		$pp_ids = explode(';', $request->pp_ids);
		$withdraw = $request->withdraw;

		PublisherPayout::whereIn('id', $pp_ids)->update(['status' => 'Approved']);

		$res = User::select('id', 'name', 'email', 'balance')->where('id', $publisher_id)->with(['publisherVidpopup' => function($query) use($prevMonth) {
			$query->where('status', 'Approved')->with('vidgen:id,cost');
			$query->where('status', 'Approved')->withCount([
				'metrics as metrics_view_count',
				'metrics as metrics_click_count' => function($query1) {
					$query1->where('status', 'click');
				},
				'metrics as metrics_pending_count' => function($query1) use($prevMonth)  {
					$query1->where('status', 'click')->where('paid_status', 'Pending')->where('updated_at', '<', $prevMonth);
				},
				'metrics as metrics_paid_count' => function($query1) {
					$query1->where('status', 'click')->where('paid_status', 'Paid');
				},
			]);
		}])
		->with(['publisherPayout' => function($query) {
			$query->orderBy('updated_at', 'desc')->orderBy('id', 'desc');
		}])
		->withCount(['publisherPayout as publisher_payout_count' => function($query) {
			$query->where('status', 'Pending');
		}])->get();
		return response()->json($res, 200);
	}

	// public function getAdminPayPublisher(Request $request) {
	// 	$year = $request->year;
	// 	$month = $request->month;
	// 	$lastday = $request->lastday;
	// 	$start_date = $year.'-'.($month < 10 ? '0'.$month : $month).'-01 00:00:00';
	// 	$end_date = $year.'-'.($month < 10 ? '0'.$month : $month).'-'.$lastday.' 23:59:50';
	// 	$res = User::select('id', 'name', 'email', 'balance')->where('role', 'publisher')
	// 		->with(['publisherVidpopup' => function($query) use ($start_date, $end_date) {
	// 				$query->where('status', 'Approved')
	// 					->with(['metrics' => function($query1) use ($start_date, $end_date) {
	// 						$query1->where('status', 'click')->where('paid_status', 'Pending')->whereBetween('updated_at', [$start_date, $end_date]);
	// 					}])
	// 					->withCount([
	// 						'metrics as metrics_click_count' => function($query1) use ($start_date, $end_date) {
	// 							$query1->where('status', 'click')->where('paid_status', 'Pending')->whereBetween('updated_at', [$start_date, $end_date]);
	// 						},
	// 					]);
	// 				$query->where('status', 'Approved')
	// 					->with('vidgen:id,cost');
	// 				$query->where('status', 'Approved')
	// 					->with('advertiser:id,name,email,balance');
	// 			}])
	// 		->get();
	// 	return response()->json($res, 200);
	// }

	// public function updateAdminPayPublisher(Request $request) {
	// 	$publisher_id = $request->publisher_id;
	// 	$year = $request->year;
	// 	$month = $request->month;
	// 	$lastday = $request->lastday;
	// 	$start_date = $year.'-'.($month < 10 ? '0'.$month : $month).'-01 00:00:00';
	// 	$end_date = $year.'-'.($month < 10 ? '0'.$month : $month).'-'.$lastday.' 23:59:50';
	// 	$row = User::select('id', 'name', 'email', 'balance')->where('id', $publisher_id)->where('role', 'publisher')
	// 		->with(['publisherVidpopup' => function($query) use ($start_date, $end_date) {
	// 				$query->where('status', 'Approved')
	// 					->with(['metrics' => function($query1) use ($start_date, $end_date) {
	// 						$query1->where('status', 'click')->where('paid_status', 'Pending')->whereBetween('updated_at', [$start_date, $end_date]);
	// 					}])
	// 					->withCount([
	// 						'metrics as metrics_click_count' => function($query1) use ($start_date, $end_date) {
	// 							$query1->where('status', 'click')->where('paid_status', 'Pending')->whereBetween('updated_at', [$start_date, $end_date]);
	// 						},
	// 					]);
	// 				$query->where('status', 'Approved')
	// 					->with('vidgen:id,cost');
	// 				$query->where('status', 'Approved')
	// 					->with('advertiser:id,name,email,balance');
	// 			}])
	// 		->get();
	// 	$revenue = 0;
	// 	$mids = array();
	// 	foreach($row as $r) {
	// 		foreach($r->publisherVidpopup as $pv) {
	// 			$adv_user = User::find($pv->creator_id);
	// 			$adv_user->balance -= $pv->vidgen->cost * $pv->metrics_click_count;
	// 			$adv_user->save();
	// 			$revenue += $pv->vidgen->cost * $pv->metrics_click_count;
	// 			foreach($pv->metrics as $m) {
	// 				array_push($mids, $m->id);
	// 			}
	// 		}
	// 	}
	// 	$pub_user = User::find($publisher_id);
	// 	$pub_user->balance += $revenue;
	// 	$pub_user->save();
	// 	VidpopupMetrics::whereIn('id', $mids)->update(['paid_status' => 'Paid']);
		
	// 	$row = User::select('id', 'name', 'email', 'balance')->where('id', $publisher_id)->where('role', 'publisher')
	// 		->with(['publisherVidpopup' => function($query) use ($start_date, $end_date) {
	// 				$query->where('status', 'Approved')
	// 					->with(['metrics' => function($query1) use ($start_date, $end_date) {
	// 						$query1->where('status', 'click')->where('paid_status', 'Pending')->whereBetween('updated_at', [$start_date, $end_date]);
	// 					}])
	// 					->withCount([
	// 						'metrics as metrics_click_count' => function($query1) use ($start_date, $end_date) {
	// 							$query1->where('status', 'click')->where('paid_status', 'Pending')->whereBetween('updated_at', [$start_date, $end_date]);
	// 						},
	// 					]);
	// 				$query->where('status', 'Approved')
	// 					->with('vidgen:id,cost');
	// 				$query->where('status', 'Approved')
	// 					->with('advertiser:id,name,email,balance');
	// 			}])
	// 		->get();
	// 	return response()->json($row, 200);
	// }
}
