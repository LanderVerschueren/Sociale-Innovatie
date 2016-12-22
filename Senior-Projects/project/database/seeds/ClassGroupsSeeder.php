<?php

use Illuminate\Database\Seeder;

class ClassGroupsSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		DB::table( 'classes' )->insert( [
			'class' => 'Event- en Project Management',
			"abbreviation" => "EPM"
		] );
		DB::table( 'classes' )->insert( [
			'class' => 'Languages & intercultural networking',
			"abbreviation" => "LNC"
		] );
		DB::table( 'classes' )->insert( [
			'class' => 'Cross Media Management',
			"abbreviation" => "XMM"
		] );
		DB::table( 'classes' )->insert( [
			'class' => 'Human Resources & Sales',
			"abbreviation" => "HRS"
		] );

		DB::table( 'class_groups' )->insert( [
			'class_id'    => 1,
			'year'  => 1,
			'class_group' => 'EPM101'
		] );
		DB::table( 'class_groups' )->insert( [
			'class_id'    => 1,
			'year'  => 1,
			'class_group' => 'EPM102'
		] );
		DB::table( 'class_groups' )->insert( [
			'class_id'    => 1,
			'year'  => 1,
			'class_group' => 'EPM103A'
		] );
		DB::table( 'class_groups' )->insert( [
			'class_id'    => 1,
			'year'  => 1,
			'class_group' => 'EPM103B'
		] );
		DB::table( 'class_groups' )->insert( [
			'class_id'    => 1,
			'year'  => 1,
			'class_group' => 'EPM104A'
		] );
		DB::table( 'class_groups' )->insert( [
			'class_id'    => 1,
			'year'  => 1,
			'class_group' => 'EPM104B'
		] );
		DB::table( 'class_groups' )->insert( [
			'class_id'    => 2,
			'year'  => 1,
			'class_group' => 'LINC'
		] );
		DB::table( 'class_groups' )->insert( [
			'class_id'    => 3,
			'year'  => 1,
			'class_group' => 'XMM101A'
		] );
		DB::table( 'class_groups' )->insert( [
			'class_id'    => 3,
			'year'  => 1,
			'class_group' => 'XMM101B'
		] );
		DB::table( 'class_groups' )->insert( [
			'class_id'    => 4,
			'year'  => 1,
			'class_group' => 'HRS101A'
		] );
		DB::table( 'class_groups' )->insert( [
			'class_id'    => 4,
			'year'  => 1,
			'class_group' => 'HRS101B'
		] );
	}
}
