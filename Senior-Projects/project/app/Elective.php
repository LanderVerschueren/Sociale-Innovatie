<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Elective
 *
 * @property int $id
 * @property string $name
 * @property string $test_date
 * @property string $start_date
 * @property string $end_date
 * @property \Carbon\Carbon $deleted_at
 * @property int $choicesAmount
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Choice[] $choices
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Result[] $results
 * @method static \Illuminate\Database\Query\Builder|\App\Elective whereChoicesAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Elective whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Elective whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Elective whereEndDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Elective whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Elective whereName($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Elective whereStartDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Elective whereTestDate($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Elective whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property-read \App\ClassAmount $classAmount
 */
class Elective extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $dates = ["deleted_at"];

	public function choices() {
		return $this->hasMany('\App\Choice');
    }

	public function results() {
		return $this->hasManyThrough('\App\Result', '\App\Choice');
    }

	public function classAmount() {
		return $this->hasMany('\App\ClassAmount');
    }
}
