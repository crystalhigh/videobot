<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use App\Models\Step;
use App\Models\Vidpop;
use App\Models\AutoResponder;
use App\Services\AutoresponderService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Utils\CommonUtils;
use Faker\Provider\Uuid;
use Illuminate\Http\Request;

class ReplyController extends Controller
{
    /**
     * @var \App\Services\AutoresponderService
     */
    private $autoresponderService;

    /**
     * Create a new controller instance.
     *
     * @param  \App\Services\AutoresponderService  $autoresponderService
     * @return void
     */
    public function __construct(AutoresponderService $autoresponderService)
    {
        $this->autoresponderService = $autoresponderService;
    }

    public function load(Request $request)
    {
        try {
            $res = $this->loadReply();
            return response()->json(['responders' => $res], 200);
        } catch (\Throwable $e) {
            Log::error('load reply: ' . $e->getMessage());
            return response()->json(['error' => 'Check error'], 400);
        }
    }

    public function save(Request $request)
    {
        try {
            $vid = Vidpop::find($request->vid);
            if (!$vid) {
                return response()->json(['error' => 'VidGen not found!'], 400);
            }
            $step = Step::find($request->sid);
            if (!$step) {
                return response()->json(['error' => 'Step not found!'], 400);
            }
            $userid =  CommonUtils::getUserId($vid->user_id);
            $firstName = $request->firstName;
            $lastName = $request->lastName;
            $email = $request->email;
            $street = $request->street;
            // $zipcode = $request->zipcode;
            $zipcode = '1';
            $phone = $request->phone;
            $auto_responder_id = -1;
            if ($firstName && $lastName && $email && $street && $zipcode && $phone) {
                // create auto-responder
                $responder = new AutoResponder();
                $responder->name = $firstName;
                $responder->name1 = $lastName;
                $responder->email = $email;
                $responder->street = $street;
                $responder->zipcode = $zipcode;
                $responder->phone = $phone;
                $responder->user_id = $vid->user_id;
                $responder->save();
                $auto_responder_id = $responder->id;
                // add to AR
                if ($vid->integration && $vid->integration !== 'csv') {
                    try {
                        $this->autoresponderService->addSubscriberToList(
                            $userid,
                            $vid->integration,
                            $vid->arlist,
                            [
                                'email' => $email,
                                'firstName' => $firstName,
                                'lastName' => $lastName,
                                'street' => $street,
                                'zipcode' => $zipcode,
                                'phone' => $phone
                            ]
                        );
                    } catch (\Throwable $e) {
                        Log::error(
                            'save to integration error: ' . $e->getMessage()
                        );
                    }
                }
            }
            if ($request->group) {
                $group = $request->group;
            } else {
                $group = Uuid::uuid();
            }
            $reply = new Reply();
            $reply->vidpop_id = $vid->id;
            $reply->step_id = $step->id;
            $reply->user_id = $vid->user_id;
            $reply->type = $request->type;
            $reply->data = $request->value;
            $reply->pv_id = $request->pv_id;
            $reply->group = $group;
            if ($auto_responder_id != -1) {
                $reply->auto_responder_id = $auto_responder_id;
            }
            $reply->save();

            return response()->json(['group' => $group], 200);
        } catch (\Throwable $e) {
            Log::error('reply save: ' . $e->getMessage());
            Log::error($e);
            return response()->json(['error' => 'Check error'], 400);
        }
    }

    public function upload(Request $request)
    {
        try {
            $fileType = $request->fileType;
            $vid = $request->vid;
            $vidpop = Vidpop::find($vid);
            if (!$vidpop) {
                Log::error('Invalid answer trying');
                return response()->json(['error' => 'Invalid action!!!'], 400);
            }

            if (!empty($_FILES['file']['tmp_name'])) {
                if (is_uploaded_file($_FILES['file']['tmp_name'])) {
                    if ($fileType == 'camera') {
                        $_file_type = 'webm';
                    } else {
                        $_file_type = pathinfo(
                            $_FILES['file']['name'],
                            PATHINFO_EXTENSION
                        );
                    }
                    $name_original = md5(time() . rand());
                    $video_name = $name_original . '.' . $_file_type;
                    $prefix = 'uploads/answers/' . $vid;

                    $filePath = storage_path(
                        'app/public/' . $prefix . '/' . $video_name
                    );
                    if (!Storage::exists($prefix)) {
                        Storage::makeDirectory($prefix);
                    }
                    if (
                        move_uploaded_file(
                            $_FILES['file']['tmp_name'],
                            $filePath
                        )
                    ) {
                        if (!Storage::disk('s3')->exists($prefix)) {
                            Storage::makeDirectory($prefix);
                        }
                        $fileContent = Storage::get($prefix . '/' . $video_name);
                        Storage::disk('s3')->put($prefix . '/' . $video_name, $fileContent, [
                            'ACL' => 'public-read-write'
                        ]);
                        Storage::delete($prefix . '/' . $video_name);
                        return response()->json(['file' => $prefix . '/' . $video_name], 200);
                    }
                }
            }
            return response()->json(['error' => 'File not found!'], 400);
        } catch (\Throwable $e) {
            Log::error('upload error: ' . $e->getMessage());
            return response()->json(['error' => 'Check error'], 400);
        }
    }

    public function uploadAudio(Request $request)
    {
        try {
            $vid = $request->vid;
            $vidpop = Vidpop::find($vid);
            if (!$vidpop) {
                Log::error('Invalid answer trying');
                return response()->json(['error' => 'Invalid action!'], 400);
            }

            if (!empty($_FILES['file']['tmp_name'])) {
                if (is_uploaded_file($_FILES['file']['tmp_name'])) {
                    $_file_type = 'mp3';
                    $name_original = md5(time() . rand());
                    $tmp = $name_original . '_.' . $_file_type;
                    $audio_name = $name_original . '.' . $_file_type;

                    $prefix = 'uploads/answers/' . $vid;
                    $filePath = storage_path('app/public/' . $prefix . '/' . $tmp);

                    if (!Storage::exists($prefix)) {
                        Storage::makeDirectory($prefix);
                    }
                    if (move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
                        if (
                            CommonUtils::createMP3(
                                storage_path('app/public/' . $prefix . '/' . $tmp),
                                storage_path('app/public/' . $prefix . '/' . $audio_name)
                            )
                        ) {
                            if (!Storage::disk('s3')->exists($prefix)) {
                                Storage::makeDirectory($prefix);
                            }
                            Storage::delete($prefix . '/' . $tmp);
                            $fileContent = Storage::get($prefix . '/' . $audio_name);
                            Storage::disk('s3')->put($prefix . '/' . $audio_name, $fileContent, [
                                'ACL' => 'public-read-write'
                            ]);
                            return response()->json(['file' => $prefix . '/' . $audio_name], 200);
                        }
                    }
                }
            }
            return response()->json(['error' => 'File not found!'], 400);
        } catch (\Throwable $e) {
            Log::error('upload audio error: ' . $e->getMessage());
            return response()->json(['error' => 'Check error'], 400);
        }
    }

    public function delete($id)
    {
        try {
            $userid = CommonUtils::getUserId();
            $reply = Reply::find($id);
            if (!$reply) {
                return response()->json(['error' => 'Reply not found!'], 400);
            }
            if ($reply->user_id != $userid) {
                return response()->json(
                    ['error' => 'You have no permission!'],
                    400
                );
            }
            $reply->delete();
            return response()->json([], 204);
        } catch (\Throwable $e) {
            Log::error('delete reply: ' . $e->getMessage());
            return response()->json(['error' => 'Server error!'], 400);
        }
    }

    public function deleteGroup($id)
    {
        try {
            $replies = Reply::where('group', $id)->get();
            if (!$replies && count($replies)) {
                return response()->json(['error' => 'Reply not found!'], 400);
            }
            foreach ($replies as $r) {
                $r->delete();
            }
            return response()->json([], 204);
        } catch (\Throwable $e) {
            Log::error('delete replies: ' . $e->getMessage());
            return response()->json(['error' => 'Server error!'], 400);
        }
    }

    public function csvDownload()
    {
        try {
            $userid = CommonUtils::getUserId();
            // former it was designed to download auto-responder
            // but updated to download all replies @requested by Fatima
            ////////////////////////////////////////////////////////////////
            // $replies = $this->loadReply();
            // $data = 'Vidpop,Email,Name,Date' . PHP_EOL;
            // foreach ($replies as $r) {
            //     if (isset($r['autoResponder'])) {
            //         $data =
            //             $data .
            //             $r['vidpop']->name .
            //             ',' .
            //             $r['autoResponder']->email .
            //             ',' .
            //             $r['autoResponder']->name .
            //             ',' .
            //             $r['autoResponder']->created_at .
            //             PHP_EOL;
            //     }
            // }
            ///////////////////////////////////////////////////////////////////
            $replies = Reply::where('user_id', $userid)
                ->with('autoResponder')
                ->with('step')
                ->with('vidpop')
                ->orderBy('group')
                ->orderBy('created_at', 'DESC')
                ->get();

            $data = 'Vidpop,Name,Email,Step,Type,Data,Date' . PHP_EOL;
            foreach ($replies as $r) {
                if (isset($r['autoResponder'])) {
                    $data = $data .
                        $r['vidpop']->name . ','
                        . $r['autoResponder']->name . ','
                        . $r['autoResponder']->email . ','
                        . $r['step']->index . ','
                        . $r['type'] . ','
                        . $r['data'] . ','
                        . $r['created_at'] . PHP_EOL;
                }
            }

            $file = 'uploads/users/' . $userid . '/ar-download.csv';
            $path = storage_path('app/public/' . $file);
            if (file_exists($path)) {
                Storage::delete($file);
            }
            file_put_contents($path, $data);
            return response()->json(['path' => $file], 200);
        } catch (\Throwable $e) {
            Log::error('csv-download: ' . $e->getMessage());
            return response()->json(['error' => 'Check Error'], 400);
        }
    }

    public function replyCounts()
    {
        $userid = CommonUtils::getUserId();
        $replies = Reply::where('user_id', $userid)
            ->where('read', 0)
            ->with('autoResponder')
            ->with('step')
            ->with('vidpop')
            ->orderBy('group')
            ->orderBy('created_at', 'DESC')
            ->get();
        $count = 0;
        foreach ($replies as $r) {
            $originator = Auth::user()->originator;
            if ($originator && strval($originator) != '0') {
                if (!$r->vidpop['assignee']) {
                    continue;
                }
                if (!str_contains($r->vidpop['assignee'], Auth::user()->id)) {
                    continue;
                }
            }
            $count++;
        }
        return $count;
    }

    public function readReply($id)
    {
        try {
            $reply = Reply::find($id);
            if (!$reply) {
                return response()->json(['error' => 'Reply not found'], 400);
            }
            $reply->read = true;
            $reply->save();
            return response()->json(['message' => 'success'], 200);
        } catch (\Throwable $e) {
            Log::error('read reply: ' . $e->getMessage());
            return response()->json(['error' => 'Check Error'], 400);
        }
    }

    private function loadReply()
    {
        $userid = CommonUtils::getUserId();
        $replies = Reply::where('user_id', $userid)
            ->with('autoResponder')
            ->with('step')
            ->with('vidpop')
            ->orderBy('group')
            ->orderBy('created_at', 'DESC')
            ->get();
        $res = [];
        $grp = '';
        $tmp = [];
        $originator = Auth::user()->originator;

        foreach ($replies as $r) {
            $user = $r->publisherVidpopup->publisher;
            if ($originator && strval($originator) != '0') {
                if (!$r->vidpop['assignee']) {
                    continue;
                }
                if (!str_contains($r->vidpop['assignee'], Auth::user()->id)) {
                    continue;
                }
            }
            if ($grp != $r->group) {
                if ($grp != '') {
                    $res[] = $tmp;
                    $tmp = [];
                }
                $tmp['group'] = $r->group;
                $tmp['vidpop'] = $r->vidpop;
                $grp = $r->group;
            }
            if (!isset($tmp['autoResponder']) && $r->autoResponder) {
                $tmp['autoResponder'] = $r->autoResponder;
            }
            $r_tmp = $r;
            unset($r_tmp->vidpop);
            unset($r_tmp->vidpop_id);
            unset($r_tmp->user_id);
            if (isset($r_tmp->autoResponder)) {
                unset($r_tmp->autoResponder);
                unset($r_tmp->auto_responder_id);
            }
            unset($r_tmp->step_id);
            $r_tmp['step'] = CommonUtils::decodeStep($r->step);
            // $r_tmp['publisher_name']=$user->name;
            $tmp['replies'][] = $r_tmp;
        }
        if (count($tmp) > 0) {
            $res[] = $tmp;
        }

        return $res;
    }
}
