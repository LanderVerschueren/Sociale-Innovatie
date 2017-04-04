<?php

namespace App;

use App\Klas;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\ClassGroup
 *
 * @property int $id
 * @property int $class_id
 * @property string $class_group
 * @property int $year
 * @property \Carbon\Carbon $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Choice[] $choices
 * @property-read \App\Klas $classes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\User[] $users
 * @method static \Illuminate\Database\Query\Builder|\App\ClassGroup whereClassGroup($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ClassGroup whereClassId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ClassGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ClassGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ClassGroup whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ClassGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ClassGroup whereYear($value)
 * @mixin \Eloquent
 */
class ClassGroup extends Model
{
	use SoftDeletes;

	protected $guarded = [];

	protected $dates = [ "deleted_at" ];

	public function classes() {
		return $this->belongsTo('\App\Klas', 'class_id');
	}

	public function users() {
		return $this->hasMany('\App\User');
	}

	public function choices() {
		return $this->belongsToMany('\App\Choice');
	}
}
