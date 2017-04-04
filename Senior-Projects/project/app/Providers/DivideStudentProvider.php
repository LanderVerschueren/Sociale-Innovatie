<?php

namespace App\Providers;

use App\Services\DivideStudent;
use Illuminate\Support\ServiceProvider;

class DivideStudentProvider extends ServiceProvider {
	/**
	 * Bootstrap the application services.
	 *
	 * @return void
	 */
	public function boot() {
		//
	}

	/**
	 * Register the application services.
	 *
	 * @return void
	 */
	public function register() {
		$this->app->singleton( DivideStudent::class, function ( $app ) {
			//return new DivideStudent();
		} );
	}

	public function provides() {
		return [ DivideStudent::class ];
	}
}
