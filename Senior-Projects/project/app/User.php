<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Elective;
use App\Choice;

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

	//Controleren of de user al een result heeft met meegegeven elective.


	public function hasNoResult(Elective $elective)
    {
        $results = $this->results()->get();
        $electiveId = $elective->id;

        foreach ($results as $result)
        {
            $choice = Choice::where('id', $result->choice_id)->first();
            if($choice->elective_id == $elective->id)
            {
                return false;
            }
        }
        return true;
    }
}