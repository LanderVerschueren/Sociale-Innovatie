<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassGroup extends Model
{
	use SoftDeletes;

	protected $guarded = [];

	protected $dates = [ "deleted_at" ];

	public function classes() {
		return $this->belongsTo('\App\Klas');
	}

	public function users() {
		return $this->hasMany('\App\User');
	}

	public function choices() {
		return $this->belongsToMany('\App\Choice');
	}
}
