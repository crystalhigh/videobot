<?php

namespace App\Http\Controllers;

use App\Models\Step;
use App\Models\Vidpop;
use App\Models\Reply;
use App\Models\Brand;
use App\User;
use App\Models\PublisherVidpopup;
use App\Models\VidpopupMetrics;
use App\Models\PublisherBan;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

use App\Utils\CommonUtils;
use Illuminate\Support\Carbon;

class VidpopController extends Controller
{
    /**
     * load all vidpops for user
     */
    public function all()
    {
        // try {
        //     $originator = Auth::user()->originator;
        //     if (!$originator || ($originator && strval($originator) == '0')) {
        //         $rows = Vidpop::where('user_id', Auth::user()->id)->orderBy('updated_at', 'DESC')->get();
        //     } else {
        //         $rows = Vidpop::where('assignee', 'LIKE', '%' . Auth::user()->id . '%')->get();
        //     }
        //     return response()->json($rows, 200);
        // } catch (\Throwable $e) {
        //     Log::error('VidGen all : ' . $e->getMessage());
        //     return response()->json(['error' => 'Check error'], 400);
        // }
        try {
            $rows = Vidpop::with('advertiser')->orderBy('updated_at', 'DESC')->get();
            return response()->json($rows, 200);
        } catch (\Throwable $e) {
            Log::error('VidGen all : ' . $e->getMessage());
            return response()->json(['error' => 'Check error'], 400);
        }
    }

    public function advertise(Request $request)
    {
        try {
            $page = $request->query('page');
            $advertisers = Vidpop::with('user')->get();
            $advertisers = $advertisers->where('user.role', '<>', 'publisher');
            $banned_list = PublisherBan::where('publisher_id', Auth::user()->id)->get();
            foreach($banned_list as $banned) {
                $advertisers = $advertisers->where('user.id', '<>', $banned->advertiser_id);
            }
            foreach($advertisers as $item) {
                $count = 0;
                $pv = PublisherVidpopup::where('vidpopup_id', $item->id)->where('status', 'Approved')->get();
                foreach($pv as $pv_item) {
                    $m = VidpopupMetrics::where('pv_id', $pv_item->id)->get();
                    $pv_item['metrics'] = $m;
                    $arrUrls = explode(';', $pv_item->website_url);
                    $count += count($arrUrls);
                }
                $step = Step::where('vidpop_id', $item->id)->get();
                $pv_last = PublisherVidpopup::where('publisher_id', Auth::user()->id)->where('vidpopup_id', $item->id)->orderBy('updated_at', 'desc')->first();
                $item['publisher_vidpopup'] = $pv;
                $item['publisher_vidpopup_last'] = $pv_last;
                $item['publisher_vidpopup_cnt'] = $count;
                $item['steps'] = $step;
            }
            $advertisers = $advertisers->toArray();
            $advertisers = array_filter($advertisers, function($x) {
                return count($x['steps']) != 0;
            });
            $advertisers = array_slice($advertisers, ($page - 1) * 10, 30);
            return response()->json($advertisers, 200);
        } catch (\Throwable $e) {
            printf($e->getMessage());
            Log::error('advertise videos: ' . $e->getMessage());
            return response()->json(['error' => 'Check error'], 400);
        }
    }
    /**
     * load vidpop by id
     */
    public function load($id)
    {
        try {
            $originator = Auth::user()->originator;
            if (!$originator || ($originator && strval($originator) == '0')) {
                $vidpop = Vidpop::find($id);
            } else {
                $vidpop = Vidpop::where('id', $id)->where('assignee', 'LIKE', '%' . Auth::user()->id . '%')->first();
            }
            if (!$vidpop) {
                return response()->json(['error' => 'Permission error!', 400]);
            }
            $decoded = CommonUtils::decodeVidpop($vidpop);
            $color = '#ffffff';
            $bg_color = '#1998e4';
            $name = 'vid-gen.com';
            $url = 'https://vid-gen.com';
            if ($vidpop->brand != 'VidGen') {
                $brand = Brand::find($vidpop->brand);
                if ($brand) {
                    $color = $brand->color;
                    $bg_color = $brand->bg_color;
                    $name = $brand->name;
                    $url = $brand->url;
                }
            }
            return response()->json(['vidpop' => $decoded, 'color' => $color, 'bg_color' => $bg_color, 'brand' => $name, 'url' => $url], 200);
        } catch (\Throwable $e) {
            Log::error('get vidpop : ' . $e->getMessage());
            return response()->json(['error' => 'Check error'], 400);
        }
    }

    /**
     * save vidpop create & update to here
     */
    public function save(Request $request)
    {
        try {
            if ($request->id) {
                $row = Vidpop::find($request->id);
            } else {

                // check user role and prevent creation for publisher
                if (Auth::user()->role == 'publisher') {
                    // return forbidden
                    return response()->json(['error' => 'You have no permission to create Vidgen!'], 403);
                }
                $row = new Vidpop();
                $unique = false;
                $code = '';
                $count = 0;
                while (!$unique && $count < 10) {
                    $code = CommonUtils::generateCode(8);
                    $u = Vidpop::where('code', $code)->get();
                    if (count($u) === 0) {
                        $unique = true;
                    } else {
                        $count++;
                    }
                }
                $row->code = $code;
                $row->integration = 'csv';
                // add assignee
                $originator = Auth::user()->originator;
                if ($originator && strval($originator) !== '0') {
                    $row->assignee = Auth::user()->id;
                }
            }
            // $userid = CommonUtils::getUserId();
            $row->brand = $request->brand;
            $row->name = $request->name;
            $row->cost = $request->cost;
            $row->user_id = $request->advertiser_id;
            // $row->user_id = $userid;
            $row->end_font = $request->end_font;
            $row->end_bg = $request->end_bg;
            $row->end_color = $request->end_color;
            $row->end_position = $request->end_position;
            $row->title = $request->title ? $request->title : 'Thank you';
            $row->content = $request->content;
            $row->title_size = $request->title_size ? $request->title_size : '2.5rem';
            $row->content_size = $request->content_size ? $request->content_size : '1.5rem';
            $row->custom_color = $request->custom_color ? $request->custom_color : '#ffffff';
            $row->custom_bgcolor = $request->custom_bgcolor ? $request->custom_bgcolor : '#1998e4';
            $row->is_custom = $request->is_custom ? $request->is_custom : 0;
            $row->custom_text = $request->custom_text;
            $row->custom_link = $request->custom_link;
            $row->integration = $request->integration;
            $row->arlist = $request->arlist;
            $row->advanced = $request->advanced ? json_encode($request->advanced) : '';
            $row->social = $request->social ? json_encode($request->social) : '';
            $row->widget = $request->widget ? json_encode($request->widget) : '';
            $row->is_template = $request->is_template ? $request->is_template : 0;

            $row->save();
            return response()->json($row, 200);
        } catch (\Throwable $e) {
            Log::error('save vidpop : ' . $e->getMessage());
            return response()->json(['error' => 'Check error'], 400);
        }
    }
    /**
     * update assignee
     */
    public function saveAssignee(Request $request)
    {
        try {
            $vidpop = Vidpop::find($request->id);
            if (!$vidpop) {
                return response()->json(['error' => 'VidGen not found!'], 400);
            }
            $vidpop->assignee = $request->assignee;
            $vidpop->save();
            return response()->json(['msg' => 'success'], 200);
        } catch (\Throwable $e) {
            Log::error('update VidGen assignee: ' . $e->getMessage());
            return response()->json(['error' => 'Check error'], 400);
        }
    }
    /**
     * delete vidpop
     */
    public function delete($id)
    {
        try {
            $row = Vidpop::find($id);
            if (!$row) {
                return response()->json(['error' => 'VidGen does not exist!'], 400);
            }
            // $userid = CommonUtils::getUserId();
            // if ($row->user_id != $userid) {
            //     return response()->json(['error' => 'You are not allowed to delete the VidGen!'], 200);
            // }

            // check replies
            $reply = Reply::where('vidpop_id', $id)->get();
            foreach ($reply as $r) {
                if (($r->type === 'audio' || $r->type === 'video') && $r->data) {
                    // remove file
                    if (Storage::disk('s3')->exists($r->data)) {
                        Storage::disk('s3')->delete($r->data);
                    }
                }
                $r->delete();
            }
            // if (count($reply) > 0) {
            //     return response()->json(['error' => 'You already have reply for this VidGen!'], 200);
            // }

            // delete all steps
            $steps = Step::where('vidpop_id', $id)->get();
            foreach ($steps as $s) {
                // delete files
                if (Storage::disk('s3')->exists($s->video_url)) {
                    Storage::disk('s3')->delete([$s->video_url]);
                }
                if (Storage::disk('s3')->exists($s->thumb_url)) {
                    Storage::disk('s3')->delete([$s->thumb_url]);
                }
                try {
                    $s->delete();
                } catch (\Throwable $e) {
                    Log::error($e);
                }
            }
            $row->delete();
            return response()->json([], 204);
        } catch (\Throwable $e) {
            Log::error('delete VidGen : ' . $e->getMessage());
            return response()->json(['error' => 'Check error'], 400);
        }
    }

    public function templates()
    {
        try {
            $templates = Vidpop::where('is_template', 1)->get();
            return response()->json(['templates' => $templates], 200);
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Not found!'], 400);
        }
    }

    public function clone(Request $request)
    {
        try {
            $template = Vidpop::find($request->id);
            if (!$template || $template->is_template != 1) {
                Log::error('clone Template : ' . $request->id);
                return response()->json(['error' => 'Template not found!'], 400);
            }
            $userid = CommonUtils::getUserId();
            $vidpop = $template->replicate();
            $vidpop->created_at = Carbon::now();
            $vidpop->updated_at = Carbon::now();
            $vidpop->is_template = 0;
            $vidpop->user_id = $userid;
            if (Auth::user()->originator != '0' || Auth::user()->originator != 0) {
                // sub-user
                $vidpop->assignee = Auth::user()->id;
            }
            $unique = false;
            $code = '';
            $count = 0;
            while (!$unique && $count < 10) {
                $code = CommonUtils::generateCode(8);
                $u = Vidpop::where('code', $code)->get();
                if (count($u) === 0) {
                    $unique = true;
                } else {
                    $count++;
                }
            }
            $vidpop->code = $code;
            $vidpop->save();
            $steps = Step::where('vidpop_id', $template->id)->get();
            $old_ids = [];
            $new_ids = [];
            foreach ($steps as $s) {
                $old_ids[] = $s->id;
                $newStep = $s->replicate();
                $newStep->vidpop_id = $vidpop->id;
                $newStep->created_at = Carbon::now();
                $newStep->updated_at = Carbon::now();
                // clone storage
                $name_org = md5(time() . rand());
                if (Storage::disk('s3')->exists($s->video_url)) {
                    $ext = pathinfo($s->video_url, PATHINFO_EXTENSION);
                    $url = 'uploads/users/' . $userid . '/' . $name_org . '.' . $ext;
                    Storage::disk('s3')->copy($s->video_url, $url);
                    $newStep->video_url = $url;
                }
                if (Storage::disk('s3')->exists($s->thumb_url)) {
                    $url = 'uploads/users/' . $userid . '/' . $name_org . '_thumb.jpg';
                    Storage::disk('s3')->copy($s->thumb_url, $url);
                    $newStep->thumb_url = $url;
                }
                $newStep->save();
                $new_ids[] = $newStep->id;
            }
            // update next_step for each one
            $steps = Step::where('vidpop_id', $vidpop->id)->get();
            foreach ($steps as $s) {
                $arr = explode(',', $s->next_step);
                $res = [];
                foreach ($arr as $id) {
                    if ($id === 'end') {
                        $res[] = 'end';
                    } else {
                        // find old ids and put new ids there
                        $key = array_search($id, $old_ids);
                        if ($key > -1) {
                            $res[] = $new_ids[$key];
                        }
                    }
                }
                $s->next_step = implode(',', $res);
                $s->save();
            }
            $firstStep = Step::where('vidpop_id', $vidpop->id)->where('index', 1)->first();
            return response()->json(['vidpop' => $vidpop, 'first' => $firstStep->id], 200);
        } catch (\Throwable $e) {
            Log::error('clone Template : ' . $request->id);
            Log::error('details : ' . $e->getMessage());
            return response()->json(['error' => 'Template not found'], 400);
        }
    }

    public function cloneVidpop(Request $request)
    {
        try {
            $template = Vidpop::find($request->id);
            if (!$template) {
                Log::error('clone project : ' . $request->id);
                return response()->json(['error' => 'VidGen not found!'], 400);
            }
            $userid = CommonUtils::getUserId();
            $vidpop = $template->replicate();
            $vidpop->created_at = Carbon::now();
            $vidpop->updated_at = Carbon::now();
            $vidpop->is_template = 0;
            $vidpop->name = 'Copy of ' . $vidpop->name;
            $unique = false;
            $code = '';
            $count = 0;
            while (!$unique && $count < 10) {
                $code = CommonUtils::generateCode(8);
                $u = Vidpop::where('code', $code)->get();
                if (count($u) === 0) {
                    $unique = true;
                } else {
                    $count++;
                }
            }
            $vidpop->code = $code;
            $vidpop->save();
            $steps = Step::where('vidpop_id', $template->id)->get();
            $old_ids = [];
            $new_ids = [];
            foreach ($steps as $s) {
                $old_ids[] = $s->id;
                $newStep = $s->replicate();
                $newStep->vidpop_id = $vidpop->id;
                $newStep->created_at = Carbon::now();
                $newStep->updated_at = Carbon::now();

                $name_org = md5(time() . rand());
                if (Storage::disk('s3')->exists($s->video_url)) {
                    $ext = pathinfo($s->video_url, PATHINFO_EXTENSION);
                    $url = 'uploads/users/' . $userid . '/' . $name_org . '.' . $ext;
                    Storage::disk('s3')->copy($s->video_url, $url);
                    $newStep->video_url = $url;
                }
                if (Storage::disk('s3')->exists($s->thumb_url)) {
                    $url = 'uploads/users/' . $userid . '/' . $name_org . '_thumb.jpg';
                    Storage::disk('s3')->copy($s->thumb_url, $url);
                    $newStep->thumb_url = $url;
                }

                $newStep->save();
                // clone stroage
                $new_ids[] = $newStep->id;
            }
            $steps = Step::where('vidpop_id', $vidpop->id)->get();
            foreach ($steps as $s) {
                $arr = explode(',', $s->next_step);
                $res = [];
                foreach ($arr as $id) {
                    if ($id === 'end') {
                        $res[] = 'end';
                    } else {
                        // find old ids and put new ids there
                        $key = array_search($id, $old_ids);
                        if ($key > -1) {
                            $res[] = $new_ids[$key];
                        }
                    }
                }
                $s->next_step = implode(',', $res);
                $s->save();
            }
            $firstStep = Step::where('vidpop_id', $vidpop->id)->where('index', 1)->first();
            return response()->json(['vidpop' => $vidpop, 'first' => $firstStep->id], 200);
        } catch (\Throwable $e) {
            Log::error('clone Template : ' . $request->id);
            Log::error('details : ' . $e->getMessage());
            return response()->json(['error' => 'Template not found'], 400);
        }
    }

    public function stepCounts(Request $request, $id)
    {
        $start_date = $request->start;
        $end_date = $request->end;
        try {
            $vid = Vidpop::find($id);
            if (!$vid) {
                return response()->json(['error' => 'VidGen is not exist'], 400);
            }
            // $userid = CommonUtils::getUserId();
            // if ($vid->user_id != $userid) {
            //     return response()->json(['error' => 'You have no permission!'], 400);
            // }
            $steps = Step::where('vidpop_id', $id)->get()->count();

            $rows = VidpopupMetrics::select('publisher_vidpopup.vidpopup_id as vidpop_id', 'vidpops.name as name')->join('publisher_vidpopup', 'publisher_vidpopup.id', '=', 'vidpopup_metrics.pv_id')->join('vidpops', 'vidpops.id', '=', 'publisher_vidpopup.vidpopup_id')->where('publisher_vidpopup.vidpopup_id', $id)->where(function($q) use ($start_date, $end_date) {
                $q->where(function($query) use ($start_date, $end_date) {
                    $query->where('vidpopup_metrics.created_at', '>=', $start_date.' 00:00:00')->where('vidpopup_metrics.created_at', '<=', $end_date.' 23:59:59');
                })->orWhere(function($query) use ($start_date, $end_date) {
                    $query->where('vidpopup_metrics.updated_at', '>=', $start_date.' 00:00:00')->where('vidpopup_metrics.updated_at', '<=', $end_date.' 23:59:59');
                });
            });;
            $view_count = $rows->count();
            $click_count = $rows->where('vidpopup_metrics.status', 'click')->count();
            return response()->json(['count' => $steps, 'view_count' => $view_count, 'click_count' => $click_count], 200);
        } catch (\Throwable $e) {
            Log::error('vidpop stepCounts: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 400);
        }
    }

    public function update(Request $request, $id) {
        try {
            $vidpop = Vidpop::find($id);
            if (!$vidpop) {
                return response()->json(['error' => 'Cannot find vidpop right now!'], 400);
            }
            $vidpop->user_id = $request->advertiser_id;
            $vidpop->cost = $request->cost;
            $vidpop->save();
            return response()->json(['vidpop' => $vidpop], 200);
        } catch (\Throwable $e) {
            Log::error('update vidpop error: ' . $e->getMessage());
            return response()->json(['error' => 'Cannot update vidpop right now!'], 400);
        }
    }
}
