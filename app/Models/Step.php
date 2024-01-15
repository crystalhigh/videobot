<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;

class Step extends Model
{
    use Uuid;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];
    protected $fillable = ['creator_id', 'publisher_id', 'vidpopup_id', 'website_url', 'created_at', 'updated_at'];

    public function vidpop()
    {
        return $this->belongsTo('App\Models\Vidpop');
    }
}
