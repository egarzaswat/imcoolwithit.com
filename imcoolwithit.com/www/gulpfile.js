var gulp                = require('gulp');
var concat              = require('gulp-concat');
var watch               = require('gulp-watch');
var less                = require('gulp-less');
var path                = require('path');
var cleanCSS            = require('gulp-clean-css');
var clean               = require('gulp-clean');
var minify              = require('gulp-minifier');

// ------------------ Minify Css --------------
gulp.task('minify-css', function() {
    return gulp.src('./templates/protostar/css/*.css')
        .pipe(cleanCSS({compatibility: 'ie8'}))
        .pipe(gulp.dest('./templates/protostar/css/gulp/'))
        .pipe(concat('cool.css'))
        .pipe(gulp.dest('./templates/protostar/minify/'));
});

// ------------------ Minify Js --------------
gulp.task('minify-js', function() {
    return gulp.src('./templates/protostar/js/*.js').pipe(minify({
        minify: true,
        collapseWhitespace: true,
        conservativeCollapse: true,
        minifyJS: true
    })).pipe(gulp.dest('./templates/protostar/js/gulp/'))
        .pipe(concat('cool.js'))
        .pipe(gulp.dest('./templates/protostar/minify/'));
});

// ------------------ Watch Less ------------------
gulp.task('watch-css',function(){
    gulp.watch(['./templates/protostar/css/*.css'], ['minify-css']);
});

gulp.task('watch-js',function(){
    gulp.watch('./templates/protostar/js/*.js', ['minify-js']);
});

// ------------------ TASKS -----------------------
gulp.task('develop', ['watch-css', 'watch-js']);