<?php

namespace App\Utils;

use FFMpeg\Coordinate\TimeCode;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Utils\ResizeImage;
use FFMpeg\Format\Audio\Mp3;
use App\User;
use Illuminate\Support\Facades\Storage;

class CommonUtils
{
  public static function createThumbVideo($in, $out, $w, $h, $time = 3)
  {
    try {
      $ffmpeg = FFMpeg::create();
      $video = $ffmpeg->open($in);
      $video->frame(TimeCode::fromSeconds($time))->save($out);
      $resize = new ResizeImage($out);
      $resize->resizeTo($w, $h, 'maxwidth');
      $resize->saveImage($out);
    } catch (\Throwable $e) {
      Log::error($e->getMessage());
      Log::error('createThumbVideo : ' . $e->getMessage());
      return false;
    }
    return true;
  }

  public static function convertVideo($in, $out)
  {
    try {
      $ffmpeg = FFMpeg::create();
      $video = $ffmpeg->open($in);
      $format = new X264();
      $format->setAudioCodec('libmp3lame');
      $video->save($format, $out);
      return true;
    } catch (\Throwable $e) {
      Log::error('convertVideo: ' . $e->getMessage());
      Log::error($e);
      return false;
    }
  }

  public static function generateCode($len)
  {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $string = '';

    for ($i = 0; $i < $len; $i++) {
      $string .= $characters[mt_rand(0, strlen($characters) - 1)];
    }

    return $string;
  }

  public static function decodeStep($s)
  {
    $step = $s;
    if ($step->overlay && $step->overlay != '') {
      if (gettype($step->overlay) == 'string') {
        $step['overlay'] = json_decode($step->overlay);
      }
    } else {
      $step['overlay'] = [
        'text' => '',
        'position' => 'TC',
        'size' => '2.5rem',
        'bg_color' => '',
      ];
    }
    if ($step->answer && $step->answer != '') {
      if (gettype($step->answer) == 'string') {
        $step['answer'] = json_decode($step->answer);
      }
    } else {
      $step['answer'] = [
        // 'type' => 0,
        // 'content' => [
        //   'is_video' => 1,
        //   'is_audio' => 1,
        //   'is_text' => 1,
        // ],
        // 'option' => [
        //   'limit' => 120
        // ],
        'type' => 2,
        'content' => '',
        'option' => '',
      ];
    }
    if ($step->video_cc && $step->video_cc != '') {
      if (gettype($step->video_cc) == 'string') {
        $step['video_cc'] = json_decode($step->video_cc);
      }
    } else {
      $step['video_cc'] = '[]';
    }
    return $step;
  }
  public static function decodeVidpop($v)
  {
    $vidpop = $v;
    if ($vidpop->advanced && $vidpop->advanced != '') {
      $vidpop['advanced'] = json_decode($vidpop->advanced);
    } else {
      $vidpop['advanced'] = [
        'show_control' => 0,
        'auto_play' => 0,
        'oversize' => 0,
        'show_title' => 0,
        'button_radius' => 16,
        'color1' => '#FFFFFF',
        'color2' => '#3490dc',
        'color3' => '#3490dc',
        'reply_noti' => 0,
        'integration' => 'csv',
      ];
    }
    if ($vidpop->social && $vidpop->social != '') {
      $vidpop['social'] = json_decode($vidpop->social);
    } else {
      $vidpop['social'] = [
        'fb_id' => '',
        'ga_id' => '',
        'gtm' => '',
        'seo' => 0,
        'description' => '',
      ];
    }
    if ($vidpop->widget && $vidpop->widget != '') {
      $vidpop['widget'] = json_decode($vidpop->widget);
    } else {
      $vidpop['widget'] = [
        'style' => 'circle',
        'position' => 'right',
        'color' => '#1998e4',
        'text' => '',
      ];
    }
    return $vidpop;
  }

  public static function createCC($path, $cc)
  {
    try {
      $data = 'WEBVTT' . PHP_EOL . PHP_EOL;
      foreach ($cc as $c) {
        $data = $data . $c->start . '.000 --> ' . $c->end . '.000 line:-4' . PHP_EOL;
        $data = $data . $c->description . PHP_EOL . PHP_EOL;
      }
      file_put_contents($path, $data);
      return true;
    } catch (\Throwable $e) {
      return false;
    }
  }

  public static function checkUserLevel($level, $target)
  {
    $levels = explode(',', $level);
    foreach ($levels as $l) {
      if (strtoupper($l) == strtoupper($target)) {
        return true;
      }
    }
    return false;
  }

  public static function randomPassword($len)
  {
    $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
    return substr(str_shuffle($chars), 0, $len);
  }

  public static function createMP3($in, $out)
  {
    try {
      $ffmpeg = FFMpeg::create();
      $audio = $ffmpeg->open($in);
      $format = new Mp3();
      $format->setAudioCodec('libmp3lame');
      $audio->save($format, $out);
      return true;
    } catch (\Throwable $e) {
      Log::error('createMP3 : ' . $e->getMessage());
      return false;
    }
  }

  public static function getUserId($id = -1)
  {
    if ($id != -1) {
      $userid = $id;
    } else {
      $userid = Auth::user()->id;
    }
    $user = User::find($userid);
    if ($user) {
      $originator = $user->originator;
      if ($originator && strval($originator) != '0') {
        return $user->originator;
      } else {
        return $userid;
      }
    } else {
      return $userid;
    }
  }

  public static function getDeepKey()
  {
    $api = Auth::user()->deep_api;
    $key = Auth::user()->deep_secret;

    $originator = Auth::user()->originator;

    if (!$api || !$key) {
      if ($originator && strval($originator) != '0') {
        // sub-user
        $userid = CommonUtils::getUserId();
        $parent_user = User::find($userid);
        $api = $parent_user->deep_api;
        $key = $parent_user->deep_secret;
        if (!$api || !$key) {
          return null;
        }
      } else {
        return null;
      }
    }
    return ['api' => $api, 'key' => $key];
  }

  public static function getRangeDate($dt)
  {
    $date = \DateTime::createFromFormat('Y-m-d H:i:s', $dt)->getTimestamp();

    $day = date('d', $date);

    $today = new \DateTime();
    $currentMon = date_create($today->format("Y-m-" . $day));

    if ($today > $currentMon) {
      $start = $currentMon->format('Y-m-d H:i:s');
      $currentMon->modify('+1 month');
      $next = $currentMon->format('Y-m-d H:i:s');
    } else {
      $next = $currentMon->format('Y-m-d H:i:s');
      $currentMon->modify('-1 month');
      $start = $currentMon->format('Y-m-d H:i:s');
    }

    return [$start, $next];
  }

  public static function addMailwizz($email, $fname, $lname)
  {
    try {
      $config = new \EmsApi\Config([
        'apiUrl' => env('MAILWIZZ_API'),
        'apiKey' => env('MAILWIZZ_KEY')
      ]);
      \EmsApi\Base::setConfig($config);
      $endpoint = new \EmsApi\Endpoint\ListSubscribers();
      $response = $endpoint->create(env('MAILWIZZ_VIDPOP'), [
        'EMAIL' => $email,
        'FNAME' => $fname,
        'LNAME' => $lname
      ]);
      if ($response && $response->status == 'success') {
        return $response->data->record->subscriber_uid;
      } else {
        return '';
      }
    } catch (\Throwable $e) {
      Log::error('addMailwizz error: ' . $e->getMessage());
      return '';
    }
  }

  public static function removeMailwizz($email)
  {
    try {
      $config = new \EmsApi\Config([
        'apiUrl' => env('MAILWIZZ_API'),
        'apiKey' => env('MAILWIZZ_KEY')
      ]);
      \EmsApi\Base::setConfig($config);
      $endpoint = new \EmsApi\Endpoint\ListSubscribers();
      $response = $endpoint->deleteByEmail(env('MAILWIZZ_VIDPOP'), $email);
      if ($response && $response->status == 'success') {
        return 'success';
      } else {
        return 'error';
      }
    } catch (\Throwable $e) {
      Log::error('removeMailwizz error: ' . $e->getMessage());
      return 'error';
    }
  }

  public static function userType()
  {
    if (in_array(Auth::user()->level, array('TIER1', 'TIER2', 'TIER3'))) {
      return 'appsumo';
    } else {
      return 'main';
    }
  }

  public static function s3Size()
  {
    $disk = Storage::disk('s3');
    $folder = 'uploads/users/' . CommonUtils::getUserId();
    $size = array_sum(array_map(function ($file) {
      return (int)$file['size'];
    }, array_filter($disk->listContents($folder, true /*<- recursive*/), function ($file) {
      return $file['type'] == 'file';
    })));
    return $size / 1048576;
  }
}
