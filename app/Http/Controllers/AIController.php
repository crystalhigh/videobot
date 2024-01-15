<?php

namespace App\Http\Controllers;

use App\User;
use App\Utils\CommonUtils;
use App\Models\AiVideo;
use App\Models\Vidpop;
use App\Models\Step;
use CURLFile;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

use GuzzleHttp\Exception\BadResponseException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AIController extends Controller
{
    //
    public function avatars()
    {
        try {
            $deep = CommonUtils::getDeepKey();
            if ($deep) {
                $api = $deep['api'];
                $key = $deep['key'];
            } else {
                return response()->json(['error' => 'Your credential for DeepWord is incorrect!'], 400);
            }

            $req = new Client();
            $url = env('DEEPWORD_URL') . '/api_get_video_actors';
            $res = $req->request('POST', $url, [
                'headers' => [
                    'api_key' => $api,
                    'secerat_key' => $key,
                ]
            ]);
            if ((int)$res->getStatusCode() == 200) {
                $data = $res->getBody()->getContents();
                $data = json_decode($data);
                return response()->json(['avatars' => $data], 200);
            }
            return response()->json(['error' => 'Your credential for DeepWord is incorrect!'], 400);
        } catch (\Throwable $e) {
            Log::error('ai avatar: ' . $e->getMessage());
            return response()->json(['error' => 'Your credential is not working properly, try other one!'], 400);
        }
    }

    public function credits()
    {
        try {
            $deep = CommonUtils::getDeepKey();
            if ($deep) {
                $api = $deep['api'];
                $key = $deep['key'];
            } else {
                return response()->json(['error' => 'Your credential for DeepWord is incorrect!'], 400);
            }

            $req = new Client();
            $url = env('DEEPWORD_URL') . '/api_get_credits';
            $res = $req->request('POST', $url, [
                'headers' => [
                    'api_key' => $api,
                    'secerat_key' => $key
                ]
            ]);
            if ((int)$res->getStatusCode() == 200) {
                $data = $res->getBody()->getContents();
                $data = json_decode($data);
                return response()->json(['credits' => $data], 200);
            }
            return response()->json(['error' => 'Your credential is not working properly, try other one!'], 400);
        } catch (\Throwable $e) {
            Log::error('ai credits: ' . $e->getMessage());
            return response()->json(['error' => 'Your credential is not working properly, try other one!'], 400);
        }
    }

    public function tts(Request $request)
    {
        try {
            $userid = CommonUtils::getUserId();
            $deep = CommonUtils::getDeepKey();
            if ($deep) {
                $api = $deep['api'];
                $key = $deep['key'];
            } else {
                return response()->json(['error' => 'Your credential for DeepWord is incorrect!'], 400);
            }


            $req = new Client();
            $url = env('DEEPWORD_URL') . '/api_text_to_speech';
            $res = $req->request('POST', $url, [
                'headers' => [
                    'api_key' => $api,
                    'secerat_key' => $key,
                ],
                'form_params' => [
                    'text' => $request->text,
                    'name' => $request->name,
                    'gender' => $request->gender,
                    'code' => $request->code
                ]
            ]);
            if ((int)$res->getStatusCode() == 200) {
                $data = $res->getBody()->getContents();
                $voice_data = base64_decode($data);
                $path = 'uploads/users/' . $userid . '/tts';
                $name = strtotime('now');
                if (!file_exists(storage_path('app/public/' . $path))) {
                    mkdir(storage_path('app/public/' . $path));
                    @chmod(storage_path('app/public/' . $path), 0755);
                }
                Storage::put($path . '/' . $name . '_.mp3', $voice_data);
                $out = $path . '/' . $name . '.mp3';
                if (CommonUtils::createMP3(storage_path('app/public/' . $path . '/' . $name . '_.mp3'), storage_path('app/public/' . $out))) {
                    Storage::delete($path . '/' . $name . '_.mp3');
                } else {
                    return response()->json(['error' => 'Cannot create MP3 file now'], 400);
                }
                return response()->json(['path' => $out], 200);
            }
        } catch (\Throwable $e) {
            Log::error('ai-tts: ' . $e->getMessage());
            return response()->json(['error' => 'Error Server!'], 400);
        }
    }

    public function uploadAudio(Request $request)
    {
        try {
            $vid = $request->vid;
            $userid = CommonUtils::getUserId();

            if (!empty($_FILES['file']['tmp_name'])) {
                if (is_uploaded_file($_FILES['file']['tmp_name'])) {
                    $_file_type = 'mp3';
                    $name_original = md5(time() . rand());
                    $tmp = $name_original . '_.' . $_file_type;
                    $audio_name = $name_original . '.' . $_file_type;

                    $prefix = 'app/public/uploads/users/' . $userid . '/ai';
                    $filePath = storage_path($prefix . '/' . $tmp);

                    if (!file_exists(storage_path($prefix))) {
                        @chmod(storage_path($prefix), 0755);
                        mkdir(storage_path($prefix), 0755, true);
                    }
                    if (move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
                        @chmod($filePath, 0755);
                        if (CommonUtils::createMP3(storage_path($prefix . '/' . $tmp), storage_path($prefix . '/' . $audio_name))) {
                            Storage::delete('uploads/users/' . $userid . '/ai/' . $tmp);
                            return response()->json(['file' => 'uploads/users/' . $userid . '/ai/' . $audio_name], 200);
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

    public function generateVideo(Request $request)
    {
        try {
            $userid = CommonUtils::getUserId();
            $curl = curl_init();

            $deep = CommonUtils::getDeepKey();
            if ($deep) {
                $api = $deep['api'];
                $key = $deep['key'];
            } else {
                return response()->json(['error' => 'Your credential for DeepWord is incorrect!'], 400);
            }

            $video_url = $request->video;
            $audio_url = storage_path('app/public/' . $request->audio);
            $name = $request->name;

            $postFields = array('audio_file' => new CURLFile($audio_url), 'name' => $name, 'our_video_actor_file_name' => $request->model);
            if ($request->bg) {
                if ($request->bgType === 'stock') {
                    $image_name = uniqid() . '.png';
                    $image_url = storage_path('app/public/' . $image_name);
                    file_put_contents($image_url, fopen($request->bg, 'r'));
                    $postFields['background_img'] = new CURLFile($image_url);
                    $postFields['green_screen'] = 'false';
                } else if ($request->bgType === 'upload') {
                    $postFields['background_img'] = new CURLFile(storage_path('app/public/' . $request->bg));
                    $postFields['green_screen'] = 'false';
                }
            }

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://login.deepword.co:3000/api/generate_video_api',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $postFields,
                CURLOPT_HTTPHEADER => array(
                    'api_key: ' . $api,
                    'secerat_key: ' . $key
                ),
            ));

            $response = curl_exec($curl);
            $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            if ($code == 200) {
                // success
                $data = json_decode($response);
                if ($data->status == 1) {
                    $aiVideo = new AiVideo();
                    $aiVideo->title = $name;
                    $aiVideo->video_id = basename($data->url);
                    $aiVideo->status = 0;
                    $aiVideo->user_id = $userid;
                    $aiVideo->save();
                    return response()->json(['msg' => $data->message], 200);
                } else if ($data->message) {
                    return response()->json(['error' => $data->message], 400);
                } else {
                    return response()->json(['error' => 'Cannot generate video now, please contact to administrator!'], 400);
                }
            } else {
                return response()->json(['error' => 'Cannot generate video now, please contact to administrator!'], 400);
            }
        } catch (\Throwable $e) {
            Log::error('generate video: ' . $e->getMessage());

            return response()->json(['error' => 'Server error!'], 400);
        }
    }

    public function generateVideo1(Request $request)
    {
        try {
            $userid = CommonUtils::getUserId();
            $deep = CommonUtils::getDeepKey();
            if ($deep) {
                $api = $deep['api'];
                $key = $deep['key'];
            } else {
                return response()->json(['error' => 'Your credential for DeepWord is incorrect!'], 400);
            }

            $video_url = $request->video;
            $audio_url = storage_path('app/public/' . $request->audio);
            $name = $request->name;
            $url = env('DEEPWORD_URL') . '/generate_video_api';
            $req = new Client();

            $res = $req->request(
                'POST',
                $url,
                [
                    'headers' => [
                        'api_key' => $api,
                        'secerat_key' => $key,
                    ],
                    'multipart' => [
                        [
                            'name' => 'video_file',
                            'contents' => new CURLFILE($video_url),
                            'filename' => 'video_file'
                        ],
                        [
                            'name' => 'audio_file',
                            'contents' => new CURLFILE($audio_url),
                            'filename' => 'audio_file'
                        ],
                        [
                            'name' => 'name',
                            'contents' => $name
                        ]
                    ]
                ],
                ['debug' => true]
            );
            if ((int)$res->getStatusCode() == 200) {
                $data = json_decode($res->getBody()->getContents());
                return response()->json($data, 200);
            }
            return response()->json(['error' => 'Cannot generate video now, please contact to administrator!'], 400);
        } catch (BadResponseException $e) {
            $res = $e->getResponse();
            $jsonBody = json_decode($res->getBody());
            return response()->json(['error' => 'Server error!'], 400);
        } catch (\Throwable $e) {
            Log::error('generate video: ' . $e->getMessage());
            return response()->json(['error' => 'Server error!'], 400);
        }
    }

    public function load()
    {
        try {
            $userid = CommonUtils::getUserId();
            $videos = AiVideo::where('user_id', $userid)->orderBy('updated_at', 'DESC')->get();
            $res = [];
            foreach ($videos as $v) {
                if ($v->status === 0) {
                    $start_date = strtotime($v->updated_at);
                    $now = strtotime('now');
                    $minutes = round(abs($now - $start_date) / 60, 2);
                    if ($minutes > 15) {
                        // download video
                        $v1 = $this->download($v);
                        if ($v1) {
                            $v1->status = 1;
                            $v1->save();
                            $res[] = $v1;
                        } else {
                            $res[] = $v;
                        }
                    } else {
                        $res[] = $v;
                    }
                } else {
                    $res[] = $v;
                }
            }
            return response()->json(['videos' => $res], 200);
        } catch (\Throwable $e) {
            Log::error('load video: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 400);
        }
    }

    public function delete($id)
    {
        try {
            $userid = CommonUtils::getUserId();
            $video = AiVideo::find($id);
            if (!$video) {
                return response()->json(['error' => 'AiVideo not found'], 400);
            }
            if ($video->user_id != $userid) {
                return response()->json(['error' => 'You have no permission for this video!'], 400);
            }
            // delete video file
            $video->delete();
            return response()->json(['status' => 'AiVideo removed!'], 200);
        } catch (\Throwable $e) {
            Log::error('delete video: ' . $e->getMessage());
            return response()->json(['error' => 'Server error'], 400);
        }
    }

    public function createStep(Request $request)
    {
        try {
            $vidpop = Vidpop::find($request->vid);
            $ai = AiVideo::find($request->aid);
            if (!$vidpop) {
                return response()->json(['error' => 'VidGen not found!'], 400);
            }
            if (!$ai) {
                return response()->json(['error' => 'AI Video not found!'], 400);
            }
            if ($request->sid != 'newStep') {
                $step = Step::find($request->sid);
                if (!$step) {
                    Log::error('Step is not exists: ' . $request->sid);
                    return response()->json(['error' => 'Step is not exist'], 400);
                }
            } else {
                $stepCount = Step::where('vidpop_id', $request->vid)->count();
                $level = Auth::user()->level;
                if (($level == 'FET' && $stepCount >= env('MAX_FET_STEP')) || (($level == 'FE' || $level == 'OTO1') && $stepCount >= env('MAX_FE_STEP'))) {
                    return response()->json(['error' => 'You have reached limit of steps for this VidGen!'], 400);
                }
                $step = new Step();
                $steps = Step::where('vidpop_id', $request->vid)->get();
                $index = $request->index;
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
            }
            $step->vidpop_id = $request->vid;
            $step->video_url = $ai->url;
            $step->thumb_url = $ai->thumbnail;
            $step->save();

            if ($step->index == 1) {
                $vidpop->thumb = $step->thumb_url;
                $vidpop->save();
            }

            return response()->json($step, 201);
        } catch (\Throwable $e) {
            Log::error('ai create step: ' . $e->getMessage());
            return response()->json(['error' => 'Check error'], 400);
        }
    }

    public static function download($v)
    {
        try {
            $userid = CommonUtils::getUserId();
            $deep = CommonUtils::getDeepKey();
            if ($deep) {
                $api = $deep['api'];
                $key = $deep['key'];
            } else {
                return response()->json(['error' => 'Your credential for DeepWord is incorrect!'], 400);
            }

            Log::info('Start downloading');
            // get download link
            $req = new Client();
            $url = env('DEEPWORD_URL') . '/api_download_video/' . $v->video_id;
            $res = $req->request('GET', $url, [
                'headers' => [
                    'api_key' => $api,
                    'secerat_key' => $key,
                ]
            ]);
            if ((int)$res->getStatusCode() == 200) {
                $data = $res->getBody()->getContents();
                $data = json_decode($data);
                $url = $data->video_url;
                $prefix = 'uploads/users/' . $userid . '/ai';
                if (!file_exists(storage_path('app/public/' . $prefix))) {
                    @chmod(storage_path('app/public/' . $prefix), 0755);
                    mkdir(storage_path('app/public/' . $prefix));
                }
                file_put_contents(storage_path('app/public/' . $prefix . '/' . $v->video_id . '.mp4'), file_get_contents($url));

                // create thumbnail
                $thumbPath = storage_path('app/public/' . $prefix . '/' . $v->video_id . '.jpg');
                if (CommonUtils::createThumbVideo(storage_path('app/public/' . $prefix . '/' . $v->video_id . '.mp4'), $thumbPath, 400, 400, 1)) {
                    $v->thumbnail = $prefix . '/' . $v->video_id . '.jpg';
                }
                $v->url = $prefix . '/' . $v->video_id . '.mp4';

                // upload to s3
                $videoContent = Storage::get($v->url);
                $thumbContent = Storage::get($v->thumbnail);

                if (!Storage::disk('s3')->exists($prefix)) {
                    Storage::disk('s3')->makeDirectory($prefix);
                }

                Storage::disk('s3')->put($v->url, $videoContent, [
                    'ACL' => 'public-read-write'
                ]);

                Storage::disk('s3')->put($v->thumbnail, $thumbContent, [
                    'ACL' => 'public-read-write'
                ]);

                Storage::delete($v->url);
                Storage::delete($v->thumbnail);
                return $v;
            } else {
            }
            return null;
        } catch (\Throwable $e) {
            return null;
        }
    }
}
