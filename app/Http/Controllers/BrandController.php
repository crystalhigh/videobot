<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use App\Models\Vidpop;
use App\Utils\CommonUtils;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BrandController extends Controller
{
    public function load(Request $request)
    {
        try {
            $userid = CommonUtils::getUserId();
            $brand = Brand::where('user_id', $userid)->get();
            return response()->json($brand, 200);
        } catch (\Throwable $e) {
            Log::error('load brand error : ' . $e->getMessage());
        }
    }

    public function uploadLogo()
    {
        try {
            $userid = CommonUtils::getUserId();
            if (!empty($_FILES['file']['tmp_name'])) {
                if (is_uploaded_file($_FILES['file']['tmp_name'])) {
                    $_file_type = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                }
                $name_original = md5(time() . rand());
                $image_name = $name_original . '.' . $_file_type;

                $prefix = 'uploads/users/' . $userid . '/brand';

                $filePath = storage_path('app/public/' . $prefix . '/' . $image_name);

                if (!Storage::exists($prefix)) {
                    Storage::makeDirectory($prefix);
                }

                if (move_uploaded_file($_FILES['file']['tmp_name'], $filePath)) {
                    @chmod($filePath, 0755);
                }

                if (!Storage::disk('s3')->exists($prefix)) {
                    Storage::disk('s3')->makeDirectory($prefix);
                }

                $fileContent = Storage::get($prefix . '/' . $image_name);
                Storage::disk('s3')->put($prefix . '/' . $image_name, $fileContent, [
                    'ACL' => 'public-read-write'
                ]);
                Storage::delete($prefix . '/' . $image_name);

                return response()->json($prefix . '/' . $image_name, 200);
            }
            return response()->json(['error' => 'File is not set'], 400);
        } catch (\Throwable $e) {
            Log::error('uploadImage: ' . $e->getMessage());
            return response()->json(['error' => 'Server Error!'], 400);
        }
    }

    public function save(Request $request)
    {
        try {
            $userid = CommonUtils::getUserId();
            if ($request->id == -1 || $request->id == '') {
                // new brand
                $brand = new Brand();
            } else {
                $brand = Brand::find($request->id);
                if (!$brand) {
                    return response()->json(['error' => 'This brand is no more exists!'], 400);
                }
                if ($brand->user_id != $userid) {
                    return response()->json(['error' => 'You have no permission to this brand'], 400);
                }
            }
            $brand->name = $request->name;
            $brand->logo = $request->logo;
            $brand->url = $request->url;
            $brand->user_id = $userid;
            $brand->bg_color = $request->bg_color;
            $brand->color = $request->color;

            $brand->save();

            return response()->json($brand, 200);
        } catch (\Throwable $e) {
            Log::error('save brand: ' . $e->getMessage());
            return response()->json(['error' => 'Server Error!'], 400);
        }
    }

    public function delete($id)
    {
        // delete brand
        try {
            $brand = Brand::find($id);
            if (!$brand) {
                return response()->json(['error' => 'Brand not found!'], 400);
            }
            if ($brand->user_id !== Auth::user()->id) {
                return response()->json(['error' => 'You are not the owner!'], 400);
            }
            $brand->delete();
            // reset vidpop
            $vidpops = Vidpop::where('brand', $id)->get();
            foreach($vidpops as $v) {
                $v->brand = 'VidGen';
                $v->save();
            }
            return response()->json('Success', 200);
        } catch (\Throwable $e) {
            Log::error('Delete brand error : ' . $e->getMessage());
            return response()->json(['error' => 'Error while update server!'], 400);
        }
    }
}
