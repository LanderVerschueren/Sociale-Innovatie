<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {
	use Notifiable;
	use SoftDeletes;

	protected $guarded = [];

	protected $dates = [ "deleted_at" ];

	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password',
		'remember_token',
	];

	public function class_groups() {
		return $this->belongsTo('\App\ClassGroup');
	}

	public function choices() {
		return $this->belongsToMany('\App\Choice', 'results');
	}

	public function results() {
		return $this->hasMany('\App\Result');
	}
}