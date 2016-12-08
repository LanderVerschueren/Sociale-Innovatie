<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Klas extends Model
{
	use SoftDeletes;

    protected $table = "classes";
    protected $guarded = [];
    protected $dates = ["deleted_at"];

	public function class_groups() {
		return $this->hasMany('\App\ClassGroup');
    }

	public function users() {
		return $this->hasManyThrough('\App\User', '\App\ClassGroup');
    }

}
