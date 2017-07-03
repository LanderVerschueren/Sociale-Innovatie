<?php

namespace App;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Elective;
use App\Choice;
use Illuminate\Support\Facades\DB;

/**
 * App\User
 *
 * @property int $id
 * @property string $surname
 * @property string $first_name
 * @property string $email
 * @property string $student_id
 * @property string $password
 * @property bool $is_admin
 * @property int $class_group_id
 * @property \Carbon\Carbon $deleted_at
 * @property string $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Choice[] $choices
 * @property-read \App\ClassGroup $class_groups
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Result[] $results
 * @method static \Illuminate\Database\Query\Builder|\App\User whereClassGroupId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereEmail($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereFirstName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereIsAdmin($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User wherePassword($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereRememberToken($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereStudentId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereSurname($value)
 * @method static \Illuminate\Database\Query\Builder|\App\User whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\ClassGroup $class_group
 */
class User extends Authenticatable
{
    use Notifiable;
    use SoftDeletes;

    protected $guarded = [];

    protected $dates = ["deleted_at"];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function class_group()
    {
        return $this->belongsTo('\App\ClassGroup');
    }

    public function choices()
    {
        return $this->belongsToMany('\App\Choice', 'results');
    }

    public function results()
    {
        return $this->hasMany('\App\Result');
    }

    public function is_admin()
    {
        if ($this->is_admin == true) {
            return true;
        } else {
            return false;
        }
    }

    //Controleren of de user al een result heeft met meegegeven elective.


    public function canAnswer(Elective $elective)
    {
        $hisElective         = false;
        $class_group_id      = $this->class_group_id;
        $choice_class_groups = DB::table('choice_class_group')->where('class_group_id', $class_group_id)->get();
        $electiveIds         = [];

        foreach ($choice_class_groups as $choice_class_group) {
            $choice = Choice::where('id', $choice_class_group->choice_id)->first();
            if ($choice) {
                array_push($electiveIds, $choice->elective_id);
            }
        }

        $uniqueElectivesId = array_unique($electiveIds);

        $electives = [];

        foreach ($uniqueElectivesId as $id) {
            $newElective = Elective::where('id', $id)->first();
            debug($newElective->name);
            $thisDate  = date("Y-m-d G:i:s");
            $beginDate = $newElective->start_date;
            $endDate   = $newElective->end_date;
            if (($thisDate <= $endDate) && ($thisDate >= $beginDate)) {
                array_push($electives, $newElective);
            }
        }

        $idRequest = $elective->id;

        foreach ($electives as $element) {
            if ($idRequest == $element->id) {
                $hisElective = true;
            }
        }

        if ( ! $hisElective) {
            return false;
        }

        if ($hisElective) {
            $results    = $this->results()->get();
            $electiveId = $elective->id;

            foreach ($results as $result) {
                $choice = Choice::where('id', $result->choice_id)->first();
                if ($choice->elective_id == $elective->id) {
                    return false;
                }
            }

            return true;
        }

    }
}