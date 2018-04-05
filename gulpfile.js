var browserify = require('browserify');
var gulp = require('gulp');
var source = require('vinyl-source-stream');
var buffer = require('vinyl-buffer');
var concat = require('gulp-concat');
var uglify = require('gulp-uglifyes');
var gutil = require('gulp-util');

gulp.task('admin-bundle', function () {
    // bundeling the essential admin javascript
    return browserify('./gulp/admin.js').bundle()
        .pipe(source('bundle.js'))
        .pipe(buffer())
        .pipe(uglify())
        .on('error', gutil.log)
        .pipe(gulp.dest('./www/admin/vendor/'));
});
gulp.task('admin-css', function () {
    // bundeling the essential admin css
    return gulp.src([
        'node_modules/jquery-ui-dist/*.min.css'])
        .pipe(concat('style.min.css'))
        .pipe(gulp.dest('./www/admin/vendor/'));
});
gulp.task('admin-copy', function () {
    // copying essential admin javascript plugins
    /*
    gulp.src([
        'node_modules/jQuery/dist/*'
    ], { base: 'node_modules/jQuery/dist' })
        .pipe(gulp.dest('./www/admin/vendor/jQuery'));
    */
    gulp.src([
        'node_modules/dropzone/dist/**/*',
        'node_modules/prettyPhoto/**/*',
        'node_modules/ckeditor/**/*',
        'node_modules/tinymce/**/*',
        'node_modules/nestedSortable/jquery.mjs.nestedSortable.js'
    ], { base: 'node_modules' })
        .pipe(gulp.dest('./www/admin/vendor'));
    gulp.src([
        'bower_components/Javascript InfoVis Toolkit/Jit/*.js'
    ],{ base: 'bower_components/Javascript InfoVis Toolkit'})
        .pipe(gulp.dest('./www/admin/vendor'));
});
gulp.task('app-bundle', function () {
    // bundling essential app javascript
    return browserify('./gulp/app.js').bundle()
        .pipe(source('bundle.js'))
        .pipe(buffer())
        //.pipe(uglify())
        .on('error', gutil.log)
        .pipe(gulp.dest('./www/app/vendor/'));
});
gulp.task('app-copy', function () {
    // copying essential app javascript plugins
    /*
    gulp.src([
        'node_modules/jQuery/dist/*'
    ], { base: 'node_modules/jQuery/dist' })
        .pipe(gulp.dest('./www/app/vendor/jQuery'));
    */
    gulp.src([
        'node_modules/prettyPhoto/**/*',
        'node_modules/ionicons/dist/css/*',
        'node_modules/ionicons/dist/fonts/*'
    ], { base: 'node_modules' })
        .pipe(gulp.dest('./www/app/vendor'));
    gulp.src([
        'node_modules/@fancyapps/fancybox/dist/**/*'
    ], { base: 'node_modules/@fancyapps/fancybox/dist/' })
        .pipe(gulp.dest('./www/app/vendor/fancybox'));
});
gulp.task('default',
    [
        'admin-bundle',
        'admin-css',
        'admin-copy',
        'app-bundle',
        'app-copy'
    ]
);
