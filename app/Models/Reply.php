<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;

class Reply extends Model
{
    use Uuid;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];
    //
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function vidpop()
    {
        return $this->belongsTo('App\Models\Vidpop');
    }
    public function step()
    {
        return $this->belongsTo('App\Models\Step');
    }
    public function autoResponder()
    {
        return $this->belongsTo('App\Models\AutoResponder');
    }
    public function publisherVidpopup()
    {
        return $this->belongsTo('App\Models\PublisherVidpopup', 'pv_id');
    }
}
