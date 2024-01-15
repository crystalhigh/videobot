<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublisherPayout extends Model
{
	//
	protected $table = 'publisher_payout';
	public function publisher()
	{
		return $this->belongsTo('App\User', 'publisher_id');
	}
}
