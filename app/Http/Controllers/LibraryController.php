<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;
use App\Models\Upload;
use App\Models\Step;
use App\Models\Vidpop;
use App\Utils\CommonUtils;
use Illuminate\Support\Facades\Storage;

class LibraryController extends Controller
{
    //
    public function pixabay(Request $request)
    {
        $key = env('PIXABAY_KEY');
        $req = new Client();
        $query = 'key=' . $key;
        $page = $request->page;
        if ($request->text && $request->text != '') {
            $query = $query . '&q=' . $request->text;
        }
        $url = env('PIXABAY_URL') . 'videos?' . $query . '&per_page=200&page=' . $page;
        try {
            $res = $req->request('GET', $url);
            if ((int)$res->getStatusCode() === 200) {
                $data = $res->getBody()->getContents();
                $data = json_decode($data);
                return response()->json(['videos' => $data], 200);
            }
        } catch (\Throwable $e) {
            Log::error('Loading pixabay: ' . $e->getMessage());
            Log::error($e);
            return response()->json(['video' => []], 400);
        }
    }

    public function pixels(Request $request)
    {
        $key = env('PIXELS_KEY');
        $req = new Client();
        $url = env('PEXELS_URL');
        if ($request->text && $request->text != '') {
            // search
            $url = $url . 'search?query=' . $request->text . '&per_page=80&page=' . $request->page;
        } else {
            $url = $url . 'popular?per_page=80&page=' . $request->page;
        }
        try {
            $res = $req->request('GET', $url, [
                'headers' => [
                    'Authorization' => $key
                ]
            ]);
            if ((int)$res->getStatusCode() === 200) {
                $data = $res->getBody()->getContents();
                $data = json_decode($data);
                return response()->json(['videos' => $data], 200);
            }
            return response()->json(['videos' => []], 200);
        } catch (\Throwable $e) {
            Log::error('Loading pixels: ' . $e->getMessage());
            Log::error($e);
            return response()->json(['video' => []], 400);
        }
    }

    public function uploadedVideos(Request $request)
    {
        try {
            $userid = CommonUtils::getUserId();
            $uploads = Upload::where('user_id', $userid)->where('type', 0)->get();
            if ($uploads) {
                return response()->json(['videos' => $uploads], 200);
            } else {
                return response()->json(['videos' => []], 200);
            }
        } catch (\Throwable $e) {
            Log::error('Loading uploads: ' . $e->getMessage());
            return response()->json(['video' => []], 400);
        }
    }

    public function uploadedImages()
    {
        try {
            $userid = CommonUtils::getUserId();
            $uploads = Upload::where('user_id', $userid)->where('type', 1)->get();
            if ($uploads) {
                return response()->json(['images' => $uploads], 200);
            } else {
                return response()->json(['images' => []], 200);
            }
        } catch (\Throwable $e) {
            Log::error('Loading uploadImages: ' . $e->getMessage());
            return response()->json(['images' => []], 400);
        }
    }

    public function giphy(Request $request)
    {
        $key = env('GIPHY_KEY');
        $req = new Client();
        $query = 'api_key=' . $key . '&page=' . $request->page . '&offset=' . ($request->page - 1) * 50;
        $url = env('GIPHY_URL');
        if ($request->text && $request->text != '') {
            $query = $query . '&q=' . $request->text;
            $url = $url . 'search?' . $query;
        } else {
            $url = $url . 'trending?' . $query;
        }
        $url = $url . '&limit=50';
        try {
            $res = $req->request('GET', $url);
            if ((int)$res->getStatusCode() === 200) {
                $data = $res->getBody()->getContents();
                $data = json_decode($data);
                return response()->json(['videos' => $data], 200);
            }
            return response()->json(['videos' => []], 200);
        } catch (\Throwable $e) {
            Log::error('Loading giphy: ' . $e->getMessage());
            Log::error($e);
            return response()->json(['video' => []], 400);
        }
    }

    public function createStepLibrary(Request $request)
    {
        try {
            $userid = CommonUtils::getUserId();
            // download video
            $vpath = '';
            $tpath = '';
            $vidpop = Vidpop::find($request->vid);
            if (!$vidpop) {
                return response()->json(['error' => 'VidGen not found!'], 400);
            }
            if ($request->sid != 'newStep') {
                $step = Step::find($request->sid);
                if (!$step) {
                    Log::error('Step is not exists:' . $request->sid);
                    return response()->json(['error' => 'Step is not exist'], 400);
                }
            } else {
                // check limitation
                $stepCount = Step::where('vidpop_id', $request->vid)->count();
                $level = Auth::user()->level;
                if (($level == 'FET' && $stepCount >= env('MAX_FET_STEP')) || ($level == 'FE' && $stepCount >= env('MAX_FE_STEP'))) {
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
            if ($request->type == 'uploads') {
                $name = md5(time() . rand());
                $video_name = $name . '.mp4';
                $thumb_name = $name . '_thumb.jpg';
                if (!file_exists(storage_path('app/public/uploads'))) {
                    mkdir(storage_path('app/public/uploads'));
                }
                if (!file_exists(storage_path('app/public/uploads/users/' . $userid))) {
                    mkdir(storage_path('app/public/uploads/users/' . $userid), 0777, true);
                }
                copy($request->url, storage_path('app/public/uploads/users/' . $userid . '/' . $video_name));
                copy($request->thumb, storage_path('app/public/uploads/users/' . $userid . '/' . $thumb_name));
                // save to s3
                $vpath = 'uploads/users/' . $userid . '/' . $video_name;
                $tpath = 'uploads/users/' . $userid . '/' . $thumb_name;
                $vContent = Storage::get($vpath);
                $tContent = Storage::get($tpath);
                if (!Storage::exists('uploads/users/' . $userid)) {
                    Storage::makeDirectory('uploads/users/' . $userid);
                }
                Storage::disk('s3')->put($vpath, $vContent, [
                    'ACL' => 'public-read-write',
                ]);
                Storage::disk('s3')->put($tpath, $tContent, [
                    'ACL' => 'public-read-write'
                ]);
                if (!Storage::delete($vpath)) {
                    Log::info('failed to delete storage file try with app public');
                    Storage::delete('app/public/' . $vpath);
                }
                Storage::delete($tpath);
            } else {
                $vpath = $request->url;
                $tpath = $request->thumb;
            }
            $step->vidpop_id = $request->vid;
            $step->video_url = $vpath;
            $step->thumb_url = $tpath;
            $step->save();

            if ($step->index == 1) {
                $vidpop->thumb = $step->thumb_url;
                $vidpop->save();
            }

            return response()->json($step, 201);
        } catch (\Throwable $e) {
            Log::error('Downloading from ' . $request->type);
            Log::error('Downloading Library :' . $e->getMessage());
            return response()->json(['error' => 'Check error'], 400);
        }
    }

    public function pixabayImage(Request $request)
    {
        try {
            $key = env('PIXABAY_KEY');
            $req = new Client();
            $query = 'key=' . $key;
            if ($request->text && $request->text != '') {
                $query = $query . '&q=' . $request->text;
            }
            $url = env('PIXABAY_URL') . '?' . $query . '&per_page=200&page=' . $request->page . '&image_type=photo&orientation=horizontal&min_width=1920&min_height=1080';
            $res = $req->request('GET', $url);
            if ((int)$res->getStatusCode() === 200) {
                $data = $res->getBody()->getContents();
                $data = json_decode($data);
                return response()->json(['images' => $data], 200);
            }
        } catch (\Throwable $e) {
            Log::error('Loading pixabay: ' . $e->getMessage());
            return response()->json(['images' => []], 400);
        }
    }

    public function uploadImage()
    {
        try {
            $userid = CommonUtils::getUserId();
            if (!empty($_FILES['file']['tmp_name'])) {
                if (is_uploaded_file($_FILES['file']['tmp_name'])) {
                    $_file_type = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                }
                $name_original = md5(time() . rand());
                $image_name = $name_original . '.' . $_file_type;

                $prefix = 'uploads/users/' . $userid;

                $filePath = storage_path('app/public/' . $prefix . '/' . $image_name);

                if (!file_exists(storage_path('app/public/uploads'))) {
                    mkdir(storage_path('app/public/uploads'));
                }
                if (!file_exists(storage_path('app/public/' . $prefix))) {
                    mkdir(storage_path('app/public/' . $prefix));
                }
                if (move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
                    @chmod($filePath, 0755);
                }
                $upload = new Upload();
                $upload->user_id = $userid;
                $upload->path = $prefix . '/' . $image_name;
                $upload->thumbnail = $prefix . '/' . $image_name;
                $upload->type = 1;
                $upload->save();

                return response()->json($upload, 201);
            }
            return response()->json(['error' => 'File is not set'], 400);
        } catch (\Throwable $e) {
            Log::error('uploadImage: ' . $e->getMessage());
            return response()->json(['error' => 'Server Error!'], 400);
        }
    }
}
