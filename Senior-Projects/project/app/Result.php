<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Result
 *
 * @property int $id
 * @property int $user_id
 * @property int $choice_id
 * @property int $likeness
 * @property \Carbon\Carbon $deleted_at
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Choice $choices
 * @property-read \App\User $users
 * @method static \Illuminate\Database\Query\Builder|\App\Result whereChoiceId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Result whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Result whereDeletedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Result whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Result whereLikeness($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Result whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Result whereUserId($value)
 * @mixin \Eloquent
 */
class Result extends Model
{
	use SoftDeletes;

	protected $guarded = [];

	protected $dates = [ "deleted_at" ];

	public function users() {
		return $this->belongsTo('\App\User', 'user_id');
	}

	public function choices() {
		return $this->belongsTo('\App\Choice', 'choice_id');
	}
}
