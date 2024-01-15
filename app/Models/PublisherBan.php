<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublisherBan extends Model
{
    //
    protected $table = 'publisher_ban';
    public function publisher()
    {
        return $this->belongsTo('App\User', 'publisher_id');
    }
    public function advertiser()
    {
        return $this->belongsTo('App\User', 'advertiser_id');
    }
}
