var browserify = require('browserify');
var gulp = require('gulp');
var source = require('vinyl-source-stream');
var buffer = require('vinyl-buffer');
var uglify = require('gulp-uglifyes');
var sourcemaps = require('gulp-sourcemaps');
var gutil = require('gulp-util');

gulp.task('admin', function () {
    return browserify('./gulp/js/admin.js').bundle()
        .pipe(source('bundle.js'))
        .pipe(buffer())
        .pipe(uglify())
        .on('error', gutil.log)
        .pipe(gulp.dest('./www/admin/javascript/'));
});
gulp.task('app', function () {
    return browserify('./gulp/js/app.js').bundle()
        .pipe(source('bundle.js'))
        .pipe(buffer())
        .pipe(uglify())
        .on('error', gutil.log)
        .pipe(gulp.dest('./www/app/javascript/'));
});

