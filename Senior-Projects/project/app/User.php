<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Elective;
use App\Choice;
use Illuminate\Support\Facades\DB;

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


	public function canAnswer(Elective $elective)
    {
        $hisElective = false;
        $class_group_id = $this->class_group_id;
        $choice_class_groups = DB::table('choice_class_group')->where('class_group_id', $class_group_id)->get();
        $electiveIds = [];

        foreach($choice_class_groups as $choice_class_group)
        {
            $choice = Choice::where('id', $choice_class_group->choice_id)->first();
            array_push($electiveIds, $choice->elective_id);
        }

        $uniqueElectivesId = array_unique ( $electiveIds );

        $electives = [];

        foreach ($uniqueElectivesId as $id)
        {
            $newElective = Elective::where('id', $id)->first();
            debug($newElective->name);
            $thisDate = date("Y-m-d G:i:s");
            $beginDate = $newElective->start_date;
            $endDate = $newElective->end_date;
            if(($thisDate<=$endDate) && ($thisDate>=$beginDate))
            {
                array_push($electives, $newElective);
            }
        }

        $idRequest = $elective->id;

        foreach ($electives as $element){
            if($idRequest == $element->id){
                $hisElective = true;
            }
        }

        if(!$hisElective){
            return false;
        }

        if($hisElective){
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
}