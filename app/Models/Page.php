<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;

class Page extends Model
{
    //
    use Uuid;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];
}
