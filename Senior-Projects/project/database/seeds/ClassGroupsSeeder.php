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
			'year'  => 1,
			'class' => 'Event- en Project Management',
		] );
		DB::table( 'classes' )->insert( [
			'year'  => 1,
			'class' => 'Languages & intercultural networking',
		] );
		DB::table( 'classes' )->insert( [
			'year'  => 1,
			'class' => 'Cross Media Management',
		] );
		DB::table( 'classes' )->insert( [
			'year'  => 1,
			'class' => 'Human Resources & Sales',
		] );

		DB::table( 'class_groups' )->insert( [
			'class_id'    => 1,
			'class_group' => 'EPM101'
		] );
		DB::table( 'class_groups' )->insert( [
			'class_id'    => 1,
			'class_group' => 'EPM102'
		] );
		DB::table( 'class_groups' )->insert( [
			'class_id'    => 1,
			'class_group' => 'EPM103A'
		] );
		DB::table( 'class_groups' )->insert( [
			'class_id'    => 1,
			'class_group' => 'EPM103B'
		] );
		DB::table( 'class_groups' )->insert( [
			'class_id'    => 1,
			'class_group' => 'EPM104A'
		] );
		DB::table( 'class_groups' )->insert( [
			'class_id'    => 1,
			'class_group' => 'EPM104B'
		] );
		DB::table( 'class_groups' )->insert( [
			'class_id'    => 2,
			'class_group' => 'LINC'
		] );
		DB::table( 'class_groups' )->insert( [
			'class_id'    => 3,
			'class_group' => 'XMM101A'
		] );
		DB::table( 'class_groups' )->insert( [
			'class_id'    => 3,
			'class_group' => 'XMM101B'
		] );
		DB::table( 'class_groups' )->insert( [
			'class_id'    => 4,
			'class_group' => 'HRS101A'
		] );
		DB::table( 'class_groups' )->insert( [
			'class_id'    => 4,
			'class_group' => 'HRS101B'
		] );
	}
}
