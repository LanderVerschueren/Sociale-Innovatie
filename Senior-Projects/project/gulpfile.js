const elixir = require('laravel-elixir');

var gulp = require('gulp'),
    php = require('gulp-connect-php');

gulp.task('serve', function() {
    php.server({
        base: './public'
    });
});

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
	mix.sass('app.scss')
		.webpack('app.js')
		.copy('resources/assets/fonts', 'public/fonts')
		.task('serve')
		.browserSync({
			proxy: "localhost:8000"
			//proxy: "sociale-innovatie.int"
		});
});
