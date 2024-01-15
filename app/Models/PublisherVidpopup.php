<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublisherVidpopup extends Model
{
    //
    protected $table = 'publisher_vidpopup';
    public function publisher()
    {
        return $this->belongsTo('App\User', 'publisher_id');
    }
    public function advertiser()
    {
        return $this->belongsTo('App\User', 'creator_id');
    }
    public function vidgen() {
        return $this->belongsTo('App\Models\Vidpop', 'vidpopup_id');
    }
    public function metrics() {
        return $this->hasMany('App\Models\VidpopupMetrics', 'pv_id');
    }
    public function publisherBan() { // get data banned by advertiser (for advertiser)
        return $this->hasMany('App\Models\PublisherBan', 'publisher_id', 'publisher_id');
    }
    public function steps() {
        return $this->hasMany('App\Models\Step', 'vidpop_id', 'vidpopup_id');
    }
    public function impression() {
        return $this->hasMany('App\Models\Impression', 'vidpop_id', 'vidpopup_id');
    }
}
