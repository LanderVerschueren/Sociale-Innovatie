<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Elective extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $dates = ["deleted_at"];

	public function choices() {
		return $this->belongsTo('\App\Choice');
    }

	public function class_groups() {
		return $this->belongsToMany('\App\ClassGroup', 'choices');
    }
}
