<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassGroup extends Model
{
	use SoftDeletes;

	protected $guarded = [];

	protected $dates = [ "deleted_at" ];

	public function users() {
		return $this->hasMany('\App\User');
	}

	public function electives() {
		return $this->belongsToMany('\App\Elective', 'choices');
	}
}
