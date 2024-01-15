<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Models\Step;
use App\Models\Reply;
use App\Models\Vidpop;
use App\Models\Upload;
use App\Models\PublisherVidpopup;
use App\Utils\CommonUtils;

class StepController extends Controller
{
    /**
     * create new step
     */
    public function create(Request $request)
    {
        try {
            $vid = $request->vid;
            $fileType = $request->fileType;
            $vidpop = Vidpop::find($vid);
            $stepId = $request->sid;
            $userid = CommonUtils::getUserId();
            // if ($vidpop->user_id != $userid) {
            //     return response()->json(['error' => 'You have no permission for the VidGen'], 400);
            // }
            if ($stepId === 'newStep') {
                $stepCount = Step::where('vidpop_id', $request->vid)->count();
                $level = Auth::user()->level;
                if (($level == 'FET' && $stepCount >= env('MAX_FET_STEP')) || ($level == 'FE' && $stepCount >= env('MAX_FE_STEP'))) {
                    return response()->json(['error' => 'You have reached limit of steps for this VidGen!'], 400);
                }
                $step = new Step();
                // calculate step
                $steps = Step::where('vidpop_id', $vidpop->id)->get();
                $index = (int)$request->index;
                if ($index == -1) {
                    $step->index = count($steps) + 1;
                } else {
                    $step->index = $index + 1;
                    // update other steps index
                    foreach ($steps as $s) {
                        if ($s->index > $index) {
                            $s->index += 1;
                            $s->save();
                        }
                    }
                }
                $step->next_step = 'end';
            } else {
                $step = Step::find($stepId);
                if (!$step) {
                    Log::error('Step is not exists');
                    return response()->json(['error' => 'Step is not exist'], 400);
                }
            }
            if (!empty($_FILES['file']['tmp_name'])) {
                if (is_uploaded_file($_FILES['file']['tmp_name'])) {
                    if ($fileType == 'camera') {
                        $_file_type = 'webm';
                    } else {
                        $_file_type = pathinfo($_FILES['file']['tmp_name'], PATHINFO_EXTENSION);
                        Log::info($_FILES['file']['tmp_name']);
                        Log::info('extract extension: ' . $_file_type);
                    }
                    if (!$_file_type) {
                        $_file_type = 'mp4';
                    }

                    $name_original = md5(time() . rand());

                    $video_name = $name_original . '.' . $_file_type;
                    $thumb_name = $name_original . '_thumb.jpg';
                    $prefix = 'uploads/users/' . $userid;

                    $filePath = storage_path('app/public/' . $prefix . '/' . $video_name);

                    // if ($fileType == 'camera') {
                    //     $filePath = storage_path('app/public/' . $prefix . '/' . $name_original . '_webm' . $_file_type);
                    // } else {
                    // }

                    if (!Storage::exists($prefix)) {
                        Storage::makeDirectory($prefix);
                    }


                    if (move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
                        @chmod($filePath, 0755);

                        Log::info('File uploaded');

                        $thumbPath = storage_path('app/public/' . $prefix . '/' . $thumb_name);
                        CommonUtils::createThumbVideo($filePath, $thumbPath, 400, 400, 0);

                        Log::info('Thumbnail created');

                        // upload to s3 and delete it
                        $videoContent = Storage::get($prefix . '/' . $video_name);
                        $thumbContent = Storage::get($prefix . '/' . $thumb_name);
                        if (!Storage::disk('s3')->exists($prefix)) {
                            Storage::disk('s3')->makeDirectory($prefix);
                        }

                        Storage::disk('s3')->put($prefix . '/' . $video_name, $videoContent, [
                            'ACL' => 'public-read-write'
                        ]);

                        Storage::disk('s3')->put($prefix . '/' . $thumb_name, $thumbContent, [
                            'ACL' => 'public-read-write'
                        ]);

                        Storage::delete($prefix . '/' . $video_name);
                        Storage::delete($prefix . '/' . $thumb_name);


                        $step->vidpop_id = $vid;
                        $step->video_url = $prefix . '/' . $video_name;
                        $step->thumb_url = $prefix . '/' . $thumb_name;
                        $step->save();

                        if ($step->index === 1) {
                            $vidpop->thumb = $step->thumb_url;
                            $vidpop->save();
                        }

                        // create user uploads
                        $upload = new Upload();
                        $upload->user_id = $userid;
                        $upload->path = $prefix . '/' . $video_name;
                        $upload->thumbnail = $prefix . '/' . $thumb_name;
                        $upload->type = 0;
                        $upload->save();
                        return response()->json($step, 201);
                    }
                }
            }
            return response()->json(['error' => 'No file found!'], 400);
        } catch (\Throwable $e) {
            Log::error('upload video of VidGen : ' . $e->getMessage());
            return response()->json(['error' => 'Check error'], 400);
        }
    }

    // get single step by id and vidpop_id
    public function get(Request $request, $id)
    {
        try {
            $userid = CommonUtils::getUserId();
            $v_id = $request->v_id;
            $step = Step::find($id);
            if ($step && $step->vidpop_id == $v_id) {
                $decoded = CommonUtils::decodeStep($step);
                return response()->json($decoded, 200);
            } else if ($step && $step->vidpop_id != $v_id) {
                Log::info('stranger action : ' . $userid);
                return response()->json(['error' => 'Incorrect step id!'], 400);
            } else {
                return response()->json(['error' => 'Step not found!'], 400);
            }
        } catch (\Throwable $e) {
            Log::error('load step: ' . $e->getMessage());
            return response()->json(['error' => 'Check error'], 400);
        }
    }

    public function firstStep(Request $request)
    {
        try {
            $v_id = $request->v_id;
            $vidpop = Vidpop::find($v_id);
            if ($vidpop) {
                $step = Step::where('vidpop_id', $v_id)->orderBy('created_at')->first();
                if ($step) {
                    $decoded = CommonUtils::decodeStep($step);
                    return response()->json($decoded, 200);
                } else {
                    return response()->json(['error' => 'Step not found!'], 400);
                }
            } else {
                return response()->json(['error' => 'VidGen is not found!'], 400);
            }
        } catch (\Throwable $e) {
            Log::error('load firstStep: ' . $e->getMessage());
            return response()->json(['error' => 'Check error'], 400);
        }
    }

    public function publishWidget(Request $request)
    {
        try {
            $v_id = $request->id;
            $user_id = Auth::user()->id;
            $vidpop = Vidpop::find($v_id);
            if ($vidpop) {
                $step = Step::where('vidpop_id', $v_id)->orderBy('created_at')->first();
                if ($step) {
                    $decoded = CommonUtils::decodeStep($step);
                    $v_url = $request->url;
                    $v_description = $request->description;
                    $pv = PublisherVidpopup::where('vidpopup_id', $v_id)->where('website_url', $v_url)->first();
                    if (!$pv) {
                        $publisherVidpopup = new PublisherVidpopup();
                        $publisherVidpopup->creator_id = $vidpop->user_id;
                        $publisherVidpopup->publisher_id = $user_id;
                        $publisherVidpopup->vidpopup_id = $v_id;
                        $publisherVidpopup->website_url = $v_url;
                        $publisherVidpopup->description = $v_description;
                        $publisherVidpopup->save();
                    } else {
                        if ($pv->status == "Pending")
                            return response()->json(['error' => 'You had already sent requests! Pending now.'], 200);
                        else if ($pv->status == "Approved")
                            return response()->json(['error' => 'This vidgen is already approved!'], 200);
                        else
                            return response()->json(['error' => 'This vidgen is rejected to copy!'], 200);
                    }
                    $pv_last = PublisherVidpopup::where('publisher_id', Auth::user()->id)->where('vidpopup_id', $v_id)->orderBy('updated_at', 'desc')->first();
                    $decoded['pv_last'] = $pv_last;
                    return response()->json($decoded, 200);
                } else {
                    return response()->json(['error' => 'Step is not created!'], 400);
                }
            } else {
                return response()->json(['error' => 'Vidgen is not found!'], 400);
            }
        } catch (\Throwable $e) {
            Log::error('load publish widget: ' . $e->getMessage());
            return response()->json(['error' => 'Check error'], 400);
        }
    }

    public function load(Request $request)
    {
        $vid = $request->vid;
        try {
            $rows = Step::where('vidpop_id', $vid)->orderBy('index')->get();
            $steps = [];
            foreach ($rows as $s) {
                $steps[] = CommonUtils::decodeStep($s);
            }
            return response()->json($steps, 200);
        } catch (\Throwable $e) {
            Log::error('vidpopSteps error : ' . $e->getMessage());
        }
    }

    public function save(Request $request)
    {
        try {
            if ($request->next_step === '' || !$request->next_step) {
                return response()->json(['error' => 'You are not ready with logic!'], 400);
            }
            $step = Step::find($request->id);
            if ($step) {
                // check to see step has already reply
                $reply = Reply::where('step_id', $step->id)->get();
                $answer = json_decode($step->answer);

                if (count($reply) > 0) {
                    // check to see answer is updated or not
                    if ($request->answer['type'] != $answer->type) {
                        return response()->json(['error' => 'This one has already replies!'], 400);
                    }
                }
                $cc = [];
                if (is_array($request->video_cc)) {
                    $cc = $request->video_cc;
                } else if ($request->video_cc) {
                    $cc = json_decode($request->video_cc);
                }
                $step->thumb_url = $request->thumb_url;
                $step->video_note = $request->video_note;
                $step->video_cc = $request->video_cc ? $request->video_cc : '[]';
                $step->fit_video = $request->fit_video;
                $step->next_step = $request->next_step;
                $step->overlay = json_encode($request->overlay);
                $step->answer = json_encode($request->answer);
                $step->video_delay = $request->video_delay;
                $step->data_collection = $request->data_collection;
                $step->save();

                // create cc if it exists
                if ($cc && count($cc)) {
                    $userid = CommonUtils::getUserId();
                    $file = 'uploads/users/' . $userid . '/' . $step->id . '.vtt';
                    $path = storage_path('app/public/' . $file);
                    if (file_exists($path)) {
                        Storage::delete($file);
                    }
                    if (CommonUtils::createCC($path, $cc)) {
                        $fileContent = Storage::get($file);
                        Storage::disk('s3')->put($file, $fileContent, [
                            'ACL' => 'public-read-write',
                        ]);
                        Storage::delete($file);
                        $step->cc_path = $file;
                        $step->save();
                    }
                }
                return response()->json([], 200);
            } else {
                return response()->json(['error' => 'Step not found!'], 400);
            }
        } catch (\Throwable $e) {
            Log::error('updateStep : ' . $e->getMessage());
            return response()->json(['error' => 'Check error'], 400);
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $row = Step::find($id);
            if (!$row) {
                return response()->json(['error' => 'Step does not exist!'], 400);
            }
            $vidpop = Vidpop::find($row->vidpop_id);
            if (!$vidpop) {
                return response()->json(['error' => 'VidGen does not exist!'], 400);
            }
            // delete relations

            // delete step
            $row->delete();
            return response()->json([], 204);
        } catch (\Throwable $e) {
            Log::error('delete step: ' . $e->getMessage());
            return response()->json(['error' => 'Check error'], 400);
        }
    }

    public function updateIndex(Request $request)
    {
        try {
            $vid = $request->vid;
            $vidpop = Vidpop::find($vid);
            $origin = $request->origin + 1;
            $updated = $request->updated + 1;
            if (!$vidpop) {
                return response()->json(['error' => 'VidGen does not exist!'], 400);
            }
            $steps = Step::where('vidpop_id', $vid)->orderBy('index')->get();
            foreach ($steps as $step) {
                if ($step->index === $origin) {
                    $step->index = $updated;
                    $step->save();
                } else if ($step->index <= $updated && $step->index > $origin && $origin < $updated) {
                    $step->index -= 1;
                    $step->save();
                } else if ($step->index >= $updated && $step->index < $origin && $origin > $updated) {
                    $step->index += 1;
                    $step->save();
                }
            }
            $steps = Step::where('vidpop_id', $vid)->orderBy('index')->get();
            return response()->json(['steps' => $steps], 200);
        } catch (\Throwable $e) {
            Log::error('step update index: ' . $e->getMessage());
            return response()->json(['error' => 'Server Error!'], 400);
        }
    }

    public function uploadSound(Request $request)
    {
        try {
            $vid = $request->vid;
            $vidpop = Vidpop::find($vid);
            if (!$vidpop) {
                return response()->json(['error' => 'VidGen is not exist!'], 400);
            }
            $userid = CommonUtils::getUserId();
            if ($vidpop->user_id != $userid) {
                return response()->json(['error' => 'You have no permission for the Vidpop'], 400);
            }
            if (!empty($_FILES['file']['tmp_name'])) {
                if (is_uploaded_file($_FILES['file']['tmp_name'])) {
                    $_file_type = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                }
                $name_original = md5(time() . rand());
                $audio_name = $name_original . '.' . $_file_type;

                $prefix = 'uploads/users/' . $userid;

                $filePath = storage_path('app/public/' . $prefix . '/' . $audio_name);

                if (!file_exists(storage_path('app/public/uploads'))) {
                    mkdir(storage_path('app/public/uploads'));
                }
                if (!file_exists(storage_path('app/public/' . $prefix))) {
                    mkdir(storage_path('app/public/' . $prefix));
                }
                if (move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
                    @chmod($filePath, 0755);
                }
                // move to s3
                $fileContent = Storage::get($prefix . '/' . $audio_name);
                Storage::disk('s3')->put($prefix . '/' . $audio_name, $fileContent, [
                    'ACL' => 'public-read-write',
                ]);
                Storage::delete($prefix . '/' . $audio_name);
                return response()->json(['path' => $prefix . '/' . $audio_name], 200);
            }
            return response()->json(['error' => 'Server Error'], 400);
        } catch (\Throwable $e) {
            Log::error('uploadSound: ' . $e->getMessage());
            return response()->json(['error' => 'Server Error!'], 400);
        }
    }
}
