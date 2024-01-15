<?php

namespace App;

use App\Utils\CommonUtils;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use GoldSpecDigital\LaravelEloquentUUID\Database\Eloquent\Uuid;

use Illuminate\Support\Facades\Log;

class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use Uuid;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $guarded = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function setLevelAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['level'] = implode(',', $value);
        } else {
            $this->attributes['level'] = $value;
        }
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function delete()
    {
        try {
            CommonUtils::removeMailwizz($this->email);
            return parent::delete();
        } catch (\Throwable $e) {
            Log::error('delete user with mailwizz' . $e->getMessage());
        }
    }
    public function publisherVidpopup() {
        return $this->hasMany('App\Models\PublisherVidpopup', 'publisher_id');
    }
    public function advertiserVidpopup() {
        return $this->hasMany('App\Models\PublisherVidpopup', 'creator_id');
    }
    public function publisherPayout() {
        return $this->hasMany('App\Models\PublisherPayout', 'publisher_id');
    }
}
