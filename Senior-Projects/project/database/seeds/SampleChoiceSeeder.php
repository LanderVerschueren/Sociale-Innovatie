<?php

use Illuminate\Database\Seeder;

class SampleChoiceSeeder extends Seeder {
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run() {
		\App\Elective::create( [
			"name"       => "Keuzevak 1",
			"start_date" => \Carbon\Carbon::now()->subDays( 5 ),
			"end_date"   => \Carbon\Carbon::now()->addDays( 8 ),
		] );

		\App\Choice::create( [
			"choice"      => "Networking - groep 2",
			"minimum"     => 5,
			"maximum"     => rand( 8, 12 ),
			"elective_id" => 1,
		] )->class_groups()->sync( [ '9', '8', '2', '3', '4', '5', '6' ] );
		\App\Choice::create( [
			"choice"      => "Projectwerking",
			"minimum"     => 5,
			"maximum"     => rand( 8, 12 ),
			"elective_id" => 1,
		] )->class_groups()->sync( [ '7', '11', '10', '9', '8' ] );
		\App\Choice::create( [
			"choice"      => "Workshop sales & customer support",
			"minimum"     => 5,
			"maximum"     => rand( 8, 12 ),
			"elective_id" => 1,
		] )->class_groups()->sync( [ '7', '9', '8', '1', '3', '4', '5', '6' ] );
		\App\Choice::create( [
			"choice"      => "Artist method class - groep 1",
			"minimum"     => 5,
			"maximum"     => rand( 8, 12 ),
			"elective_id" => 1,
		] )->class_groups()->sync( [ '7', '11', '10', '9', '8', '2', '1', '3', '4', '5', '6' ] );
		\App\Choice::create( [
			"choice"      => "Creativiteit - groep 2",
			"minimum"     => 5,
			"maximum"     => rand( 8, 12 ),
			"elective_id" => 1,
		] )->class_groups()->sync( [ '7', '11', '10', '9', '8' ] );
		\App\Choice::create( [
			"choice"      => "Fame and fortune - groep 1",
			"minimum"     => 5,
			"maximum"     => rand( 8, 12 ),
			"elective_id" => 1,
		] )->class_groups()->sync( [ '7', '11', '10', '9', '8', '2', '1', '3', '4', '5', '6' ] );
		\App\Choice::create( [
			"choice"      => "Globalisation",
			"minimum"     => 5,
			"maximum"     => rand( 8, 12 ),
			"elective_id" => 1,
		] )->class_groups()->sync( [ '11', '10', '9', '8', '2', '1', '3', '4', '5', '6' ] );
		\App\Choice::create( [
			"choice"      => "Personal Branding - Groep 2",
			"minimum"     => 5,
			"maximum"     => rand( 8, 12 ),
			"elective_id" => 1,
		] )->class_groups()->sync( [ '7', '9', '2', '3', '4', '6' ] );
		\App\Choice::create( [
			"choice"      => "Creativiteit - Groep 1",
			"minimum"     => 5,
			"maximum"     => rand( 8, 12 ),
			"elective_id" => 1,
		] )->class_groups()->sync( [ '7', '11', '10', '9', '8' ] );
		\App\Choice::create( [
			"choice"      => "Digital accounting",
			"minimum"     => 5,
			"maximum"     => rand( 8, 12 ),
			"elective_id" => 1,
		] )->class_groups()->sync( [ '7', '11', '10', '9', '8', '3', '4', '5', '6' ] );
		\App\Choice::create( [
			"choice"      => "Human Resources - Groep 2",
			"minimum"     => 5,
			"maximum"     => rand( 8, 12 ),
			"elective_id" => 1,
		] )->class_groups()->sync( [ '7', '9', '8', '2', '1', '3', '4', '5', '6' ] );
		\App\Choice::create( [
			"choice"      => "MVO I - Groep 2",
			"minimum"     => 5,
			"maximum"     => rand( 8, 12 ),
			"elective_id" => 1,
		] )->class_groups()->sync( [ '7', '11', '10', '9', '8', '3', '4', '5', '6' ] );
		\App\Choice::create( [
			"choice"      => "Starters kit ondernemen - groep 2",
			"minimum"     => 5,
			"maximum"     => rand( 8, 12 ),
			"elective_id" => 1,
		] )->class_groups()->sync( [ '7', '11', '10', '9', '8', '3', '4', '5', '6' ] );
		\App\Choice::create( [
			"choice"      => "Presenteren en visualiseren als een PRO",
			"minimum"     => 5,
			"maximum"     => rand( 8, 12 ),
			"elective_id" => 1,
		] )->class_groups()->sync( [ '7', '11', '10', '9', '8', '2', '1', '3', '4', '5', '6' ] );
		\App\Choice::create( [
			"choice"      => "Spaans",
			"minimum"     => 5,
			"maximum"     => rand( 8, 12 ),
			"elective_id" => 1,
		] )->class_groups()->sync( [ '7', '11', '10', '9', '8', '3', '4', '5', '6' ] );
		\App\Choice::create( [
			"choice"      => "Intern Management 1",
			"minimum"     => 5,
			"maximum"     => rand( 8, 12 ),
			"elective_id" => 1,
		] )->class_groups()->sync( [ '1' ] );
		\App\Choice::create( [
			"choice"      => "Marketing & Employer Branding",
			"minimum"     => 5,
			"maximum"     => rand( 8, 12 ),
			"elective_id" => 1,
		] )->class_groups()->sync( [ '1', '2', '6' ] );
		\App\Choice::create( [
			"choice"      => "Storytelling (A)",
			"minimum"     => 5,
			"maximum"     => rand( 8, 12 ),
			"elective_id" => 1,
		] )->class_groups()->sync( [ '1', '3', '5' ] );
	}
}
