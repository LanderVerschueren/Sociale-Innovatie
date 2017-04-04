<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Choice
 *
 * @property int $id
 * @property string $choice
 * @property string $description
 * @property int $minimum
 * @property int $maximum
 * @property string $settings
 * @property int $elective_id
 * @property \Carbon\Carbon $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ClassGroup[] $class_groups
 * @property-read \App\Elective $elective
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Result[] $results
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @method static \Illuminate\Database\Query\Builder|\App\Choice whereChoice($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Choice whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Choice whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Choice whereDescription($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Choice whereElectiveId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Choice whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Choice whereMaximum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Choice whereMinimum($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Choice whereSettings($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Choice whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
