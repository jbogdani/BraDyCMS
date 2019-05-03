/*jshint esversion: 6 */

const gulp  = require('gulp'),
  uglify  = require('gulp-uglify'),
  less = require('gulp-less'),
  cleanCSS = require('gulp-clean-css'),
  rename = require("gulp-rename");

const libs = {
  'jquery':     [
    'dist/jquery.min.js',
    'dist/jquery.min.map'
  ],
  'bootstrap':  [
    'dist/css/bootstrap.min.css',
    'dist/css/bootstrap.css.map',
    'dist/js/bootstrap.min.js'
  ],
  'popper.js': [
    'dist/popper.min.js',
    'dist/popper-utils.min.js'
  ],
  'bootstrap3':  [
    'dist/css/bootstrap.min.css',
    'dist/css/bootstrap.css.map',
    'dist/fonts/*',
    'dist/js/bootstrap.min.js',
    'less/**/*'
  ],
  'bootstrap-datepicker':  [
    'dist/css/bootstrap-datepicker3.min.css',
    'dist/js/bootstrap-datepicker.min.js',
    'dist/locales/*'
  ],
  'datatables.net':  [
    'js/jquery.dataTables.min.js'
  ],
  'datatables.net-bs':  [
    '**/*.min.*'
  ],
  'google-code-prettify':  [
    'bin/*'
  ],
  'jquery-nestable':  [
    'jquery.nestable.js'
  ],
  '@fancyapps': [
    'fancybox/dist/*.min.*'
  ],
  'tinymce': [
    "plugins/*/plugin.min.js",
    "plugins/*/plugin.js",
    "skins/**/*",
    "themes/**/*",
    "tinymce.min*",
    "tinymce.js"
  ],
  'fine-uploader': [
    "fine-uploader/*.gif",
    "fine-uploader/fine-uploader.min.js",
    "fine-uploader/fine-uploader.min.css"
  ],
  "font-awesome": [
    "fonts/*",
    "css/font-awesome.min*"
  ],
  "jquery-mousewheel": [
    "jquery.mousewheel.js"
  ],
  "pnotify": [
    'dist/pnotify.js',
    'dist/pnotify.css'
  ],
  "select2": [
    "dist/css/select2.min.css",
    "dist/js/*min.js",
    "dist/js/i18n/*"
  ],
  "select2-bootstrap-theme": [
    "dist/*.min.css"
  ]
};

gulp.task('moveLibs', function(done){
  Object.entries(libs).forEach(([key, vals]) => {
    vals.forEach( (v) => {
      gulp.src(`node_modules/${key}/${v}`, {base: `./node_modules/`})
        .pipe(gulp.dest(`./frontLibs/`));
    });
  });
  done();
});


gulp.task('frontCSS', function () {
  return gulp.src('sites/default/css/styles.less')
  .pipe(less().on('error', function(err){ console.log(err.message); this.emit('end'); }))
  .pipe(cleanCSS())
  .pipe(gulp.dest('sites/default/css/'));
});

gulp.task('adminCSS', function () {
  return gulp.src('less/admin.less')
  .pipe(less().on('error', function(err){ console.log(err.message); this.emit('end'); }))
  .pipe(cleanCSS())
  .pipe(gulp.dest('css/'));
});

gulp.task('adminJS', function(){
  return gulp.src('./js/admin.js')
    .pipe(uglify())
    .pipe(rename({ suffix: '.min' }))
    .pipe(gulp.dest('./js/'));
});


gulp.task('default', function(){
  gulp.watch(['./js/admin.js'], gulp.series('adminJS'));
  gulp.watch(['less/admin.less'], gulp.series('adminCSS'));
  gulp.watch('sites/default/css/*.less', gulp.series('frontCSS'));
});
