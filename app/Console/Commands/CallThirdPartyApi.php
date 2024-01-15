<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\CronJob;
use App\Models\VidpopupMetrics;
use App\Models\PublisherVidpopup;
use App\Models\Vidpop;
use App\User;

class CallThirdPartyApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'api:voluum';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Call voluum api';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
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
				$data = null;
				$cur_time = date('Y-m-d').'T'.date('h').':00:00Z';
				$cron = CronJob::orderBy('from', 'desc')->first();
				if (is_null($cron)) {
					$data = [
						'from' => '2023-01-01T00:00:00Z',
						'to' => $cur_time
					];
					$cj = new CronJob();
					$cj->from = $cur_time;
					$cj->save();
				} else {
					$data = [
						'from' => $cron->from
					];
					$cron->from = $cur_time;
					$cron->save();
				}
				$data['workspaces'] = 'bb1ee9a0-5e36-4d19-99a6-9f8c054fcdbd';
				$data['column'] = ['campaignId', 'campaignName', 'revenue', 'externalId'];
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
				$resp = Http::withHeaders([
					'Content-Type' => 'application/json; charset=utf-8',
					'Accept' => 'application/json',
					'CWAUTH-TOKEN' => $token
				])->get('https://api.voluum.com/report/conversions?'.$query);
				if ($resp->successful()) {
					$conversions = $resp->json();
					foreach($conversions['rows'] as $c) {
						$pv_id = $c['externalId'];
						if ($pv_id) {
							$vm = VidpopupMetrics::where('pv_id', $pv_id)->where('status', 'view')->orderBy('created_at', 'desc')->first();
							if ($vm) {
								$vm->status = 'click';
								$vm->save();
								$pv = PublisherVidpopup::find($pv_id);
								$vidpop = Vidpop::find($pv->vidpopup_id);
								$user_advertiser = User::find($pv->creator_id);
								$user_publisher = User::find($pv->publisher_id);
								$user_advertiser->balance -= $vidpop->cost;
								$user_advertiser->save();
								$user_publisher->balance += $vidpop->cost;
								$user_publisher->save();
							}
						}
					}
				}
			}
	}
}
