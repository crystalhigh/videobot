<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;

class Vidpop extends Model
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
    public function step() {
        return $this->hasMany('App\Models\Step', 'vidpop_id');
    }
    public function advertiser() {
        return $this->hasOne('App\User', 'id', 'user_id');
    }
}
