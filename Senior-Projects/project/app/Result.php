<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Result extends Model
{
	use SoftDeletes;

	protected $guarded = [];

	protected $dates = [ "deleted_at" ];

	public function users() {
		return $this->belongsTo('\App\User');
	}

	public function choices() {
		return $this->belongsTo('\App\Choice', 'choice_id');
	}
}
