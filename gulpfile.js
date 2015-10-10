var gulp  = require('gulp'),
  uglify  = require('gulp-uglify'),
  concat  = require('gulp-concat'),
  less = require('gulp-less'),
  minifyCSS = require('gulp-minify-css'),
  rename = require("gulp-rename");

// Minify js
var changingJS = [
  'js/jquery-2.1.3.min.js',
  'js/jquery.nestable.js',
  'js/bootstrap-3.3.5.min.js',
  'js/jquery.dataTables.js',
  'js/dataTable-bootstrap.js',
  'js/admin.js',
  'js/pnotify.custom.min.js',
  'js/bootstrap-datepicker.js',
  'js/select2.min.js',
  'js/fileuploader.js',
  'js/prettify.js'
];

gulp.task('minifyAdm', function(){
  gulp.src(changingJS)
    .pipe(concat('all.min.js'))
    .pipe(uglify())
    .pipe(gulp.dest('./js/'));
  //.pipe(reload({stream: true}));
});

gulp.task('frontCSS', function () {
  gulp.src('sites/default/css/styles.less')
    .pipe(less())
    .pipe(minifyCSS())
    .pipe(gulp.dest('sites/default/css/'));
    //.pipe(reload({stream: true}));
});

gulp.task('adminCSS', function () {
  gulp.src('less/admin.less')
    .pipe(less())
    .pipe(minifyCSS())
    .pipe(gulp.dest('css/'));
    //.pipe(reload({stream: true}));
});


gulp.task('default', [], function(){
  gulp.watch(changingJS, ['minifyAdm']);
//  gulp.watch(['index.php', 'sites/default/**/*.twig'], [reload]);
  gulp.watch(['less/**/*.less', '!less/admin.less'], ['frontCSS', 'adminCSS']);
  gulp.watch(['sites/default/css/styles.less'], ['frontCSS']);
  gulp.watch(['less/admin.less'], ['adminCSS']);
});