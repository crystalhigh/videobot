<?php

namespace App\Models;

use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;
use Illuminate\Database\Eloquent\Model;

class Impression extends Model
{
    //
    use Uuid;
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function vidpop()
    {
        return $this->belongsTo('App\Models\Vidpop');
    }
}
