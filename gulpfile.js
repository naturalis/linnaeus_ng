var browserify = require('browserify');
var gulp = require('gulp');
var source = require('vinyl-source-stream');
var buffer = require('vinyl-buffer');
var concat = require('gulp-concat');
var terser = require('gulp-terser');
var log = require('fancy-log');
var modernizr = require('gulp-modernizr');

gulp.task('modernizr', function() {
  return gulp.src('node_modules/modernizr/src/*.js')
    .pipe(modernizr())
    .pipe(gulp.dest('node_modules/modernizr/'))
});
gulp.task('admin-bundle', gulp.series('modernizr'), function () {
    // bundeling the essential admin javascript
    return browserify('./gulp/admin.js').bundle()
        .pipe(source('bundle.js'))
        .pipe(buffer())
        .pipe(terser())
        .on('error', log)
        .pipe(gulp.dest('./www/admin/vendor/'));
});
// @todo: maybe do this some other time
//gulp.task('admin-css', function () {
//    // bundeling the essential admin css
//    return gulp.src([
//        'node_modules/jquery-ui-dist/*.min.css'])
//        .pipe(concat('style.min.css'))
//        .pipe(gulp.dest('./www/admin/vendor/'));
//});
gulp.task('admin-copy', function () {
    // copying essential admin javascript plugins
    /*
    gulp.src([
        'node_modules/jQuery/dist/*'
    ], { base: 'node_modules/jQuery/dist' })
        .pipe(gulp.dest('./www/admin/vendor/jQuery'));
    */
    return gulp.src([
        'node_modules/dropzone/dist/**/*',
        'node_modules/prettyPhoto/**/*',
        'node_modules/ckeditor/**/*',
        'node_modules/tinymce/**/*',
        'node_modules/jquery-ui-dist/**/*',
        'node_modules/nestedSortable/jquery.mjs.nestedSortable.js'
    ], { base: 'node_modules' })
        .pipe(gulp.dest('./www/admin/vendor'));
});
gulp.task('app-bundle', gulp.series('modernizr'), function () {
    // bundling essential app javascript
    return browserify('./gulp/app.js').bundle()
        .pipe(source('bundle.js'))
        .pipe(buffer())
        .pipe(terser())
        .on('error', log)
        .pipe(gulp.dest('./www/app/vendor/'));
});
gulp.task('app-copy', function (done) {
    // copying essential app javascript plugins
    /*
    gulp.src([
        'node_modules/jQuery/dist/*'
    ], { base: 'node_modules/jQuery/dist' })
        .pipe(gulp.dest('./www/app/vendor/jQuery'));
    */
    gulp.src([
        'node_modules/prettyPhoto/**/*',
        'node_modules/ionicons/css/*',
        'node_modules/ionicons/fonts/*',
        'node_modules/raphael/**/*'
    ], { base: 'node_modules' })
        .pipe(gulp.dest('./www/app/vendor'));
    gulp.src([
        'node_modules/\@fancyapps/fancybox/dist/**/*'
    ], { base: 'node_modules/\@fancyapps/fancybox/dist/' })
        .pipe(gulp.dest('./www/app/vendor/fancybox'));
    done();
});
gulp.task('default',
    gulp.series(
        'admin-bundle',
        'admin-copy',
        'app-bundle',
        'app-copy')
);
