
"use strict";

var gulp = require('gulp');

var	themeName = 'ci_project_name',
	paths = {
		src: './theme/',
		dist: '../www/wp-content/themes/' + themeName + '/'
	};

var browserSync 	= require('browser-sync').create();
var runSequence 	= require('run-sequence');
var plumber 		= require('gulp-plumber');
var image 			= require('gulp-image');
var sass 			= require('gulp-sass');
var autoprefixer 	= require('gulp-autoprefixer');
var concat 			= require('gulp-concat');
var minifyCss 		= require('gulp-minify-css');
var rename 			= require('gulp-rename');
var uglify 			= require('gulp-uglify');
var clean 			= require('gulp-clean');

gulp.task('clean', function() {
	return gulp.src(paths.dist)
        .pipe(clean({force: true}));
});

var wp_files_src = [
	paths.src + 'wp_files/**/*.php',
	paths.src + 'wp_files/screenshot.png',
	paths.src + 'wp_files/style.css'
];

gulp.task('wp_files', function() {

	return gulp
			.src(wp_files_src)
			.pipe(plumber())
			.pipe(gulp.dest(paths.dist))
			.pipe(browserSync.stream());
			
});

var php_vendor_src = paths.src + 'php_vendor/vendor/**/*';

gulp.task('php_vendor', function() {

	return gulp
			.src(php_vendor_src)
			.pipe(plumber())
			.pipe(gulp.dest(paths.dist + 'vendor/'))
			.pipe(browserSync.stream());

});

var images_src = paths.src + 'images/**/*';

gulp.task('images', function() {

	return gulp
			.src(images_src)
			.pipe(plumber())
			.pipe(image())
			.pipe(gulp.dest(paths.dist + 'images/'))
			.pipe(browserSync.stream());

});

var fonts_src = paths.src + 'fonts/**/*';

gulp.task('fonts', function() {

	return gulp
			.src(fonts_src)
			.pipe(plumber())
			.pipe(gulp.dest(paths.dist + 'fonts/'))
			.pipe(browserSync.stream());

});

var lang_src = [
	paths.src + 'lang/*.mo',
	'!' + paths.src + 'lang/*.temp.mo'
];

gulp.task('lang', function() {

	return gulp
			.src(lang_src)
			.pipe(plumber())
			.pipe(gulp.dest(paths.dist + 'lang/'))
			.pipe(browserSync.stream());

});

gulp.task('scss', function() {

	return gulp
			.src(paths.src + '/scss/main.scss')
			.pipe(plumber())
			.pipe(sass({outputStyle: 'compressed'}).on('error', sass.logError))
			.pipe(autoprefixer('last 3 version','safari 5', 'ie 8', 'ie 9'))
			.pipe(rename('main.css'))
			.pipe(minifyCss())
			.pipe(gulp.dest(paths.dist + '/css/'))
			.pipe(browserSync.stream());

});

gulp.task('gmaps_scripts', function() {

	return gulp
			.src('bower_components/gmaps/gmaps.min.js')
			.pipe(plumber())
			.pipe(gulp.dest(paths.dist + 'js/'));

});

gulp.task('lib_scripts', function() {

	return gulp
			.src([
				'bower_components/lodash/lodash.min.js',
				'bower_components/fastclick/lib/fastclick.js',
				'bower_components/bootstrap/dist/js/bootstrap.min.js',
			])
			.pipe(plumber())
			.pipe(concat('lib.js'))
			.pipe(gulp.dest(paths.dist + 'js/'))
			.pipe(browserSync.stream());

});

var main_scripts_src = [
	paths.src + 'js/main.js',
];

gulp.task('main_scripts', function() {

	return gulp
			.src(main_scripts_src)
			.pipe(plumber())
			.pipe(uglify())	
			.pipe(concat('main.js'))			
			.pipe(gulp.dest(paths.dist + 'js/'))
			.pipe(browserSync.stream());

});

gulp.task('bs', ['default'], function() {
    browserSync.init({
        proxy: 'lab.ci',
        port: 1337
    });

    gulp.watch(wp_files_src, ['wp_files']);
    gulp.watch(php_vendor_src, ['php_vendor']);
    gulp.watch(images_src, ['images']);
    gulp.watch(fonts_src, ['fonts']);
    gulp.watch(lang_src, ['lang']);
    gulp.watch(paths.src + '/scss/**/*.scss', ['scss']);
    gulp.watch(main_scripts_src, ['main_scripts']);  
});

gulp.task('default', function() {
	runSequence('clean', [
		'wp_files',
		'php_vendor',
		'images',
		'fonts',
		'lang',
		'scss',
		'gmaps_scripts',
		'lib_scripts',
		'main_scripts',
	]);
});