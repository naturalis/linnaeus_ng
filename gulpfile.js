var browserify = require('browserify');
var gulp = require('gulp');
var source = require('vinyl-source-stream');
var buffer = require('vinyl-buffer');
var concat = require('gulp-concat');
var uglify = require('gulp-uglifyes');
var gutil = require('gulp-util');

gulp.task('admin-bundle', function () {
    return browserify('./gulp/js/admin.js').bundle()
        .pipe(source('bundle.js'))
        .pipe(buffer())
        .pipe(uglify())
        .on('error', gutil.log)
        .pipe(gulp.dest('./www/admin/javascript/vendor/'));
});
gulp.task('admin-css', function () {
    return gulp.src([
        'node_modules/dropzone/dist/min/*.min.css',
        'node_modules/prettyPhoto/css/*.css',
        'node_modules/jquery-ui-dist/*.min.css'])
        .pipe(concat('vendor.min.css'))
        .pipe(gulp.dest('./www/admin/style/'));
});
gulp.task('admin-copy', function () {
    gulp.src([
        'node_modules/ckeditor/**/*',
        'node_modules/tinymce/**/*',
        'node_modules/nestedSortable/jquery.mjs.nestedSortable.js'
    ], { base: 'node_modules' })
        .pipe(gulp.dest('./www/admin/vendor'));
    gulp.src(['bower_components/Javascript InfoVis Toolkit/Jit/*.js'],{ base: 'bower_components/Javascript InfoVis Toolkit'})
        .pipe(gulp.dest('./www/admin/vendor'));
});
gulp.task('app-bundle', function () {
    return browserify('./gulp/js/app.js').bundle()
        .pipe(source('bundle.js'))
        .pipe(buffer())
        .pipe(uglify())
        .on('error', gutil.log)
        .pipe(gulp.dest('./www/app/javascript/vendor/'));
});
gulp.task('app-copy', function () {
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
gulp.task('default', [ 'admin-bundle', 'admin-css', 'admin-copy', 'app-bundle', 'app-copy' ]);
