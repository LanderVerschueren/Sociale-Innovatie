<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\ClassAmount
 *
 * @property int $id
 * @property int $elective_id
 * @property int $class_id
 * @property int $amount
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Elective $elective
 * @property-read \App\Klas $klas
 * @method static \Illuminate\Database\Query\Builder|\App\ClassAmount whereAmount($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ClassAmount whereClassId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ClassAmount whereCreatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ClassAmount whereElectiveId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ClassAmount whereId($value)
 * @method static \Illuminate\Database\Query\Builder|\App\ClassAmount whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ClassAmount extends Model
{
    protected $table = 'elective_class_amount';

	protected $guarded = [];

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function elective() {
		return $this->belongsTo('\App\Elective');
	}

	/**
	 * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
	 */
	public function klas() {
		return $this->belongsTo('\App\Klas');
	}
}
