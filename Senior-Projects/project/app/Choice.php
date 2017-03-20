<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Choice extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $dates = ["deleted_at"];

	public function class_groups() {
		return $this->belongsToMany('\App\ClassGroup');
    }

	public function elective() {
		return $this->belongsTo('\App\Elective');
    }

	public function results() {
		return $this->hasMany('\App\Result');
    }

	public function users() {
		return $this->belongsToMany('\App\User', 'results');
	}
}
