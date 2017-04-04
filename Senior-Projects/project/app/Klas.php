<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Klas
 *
 * @property int $id
 * @property string $class
 * @property string $abbreviation
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\ClassGroup[] $class_groups
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @method static \Illuminate\Database\Query\Builder|\App\Klas whereAbbreviation($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Klas whereClass($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Klas whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Klas whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Klas whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Klas whereUpdatedAt($value)
 * @mixin \Eloquent
 */
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
