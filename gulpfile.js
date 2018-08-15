var gulp  = require('gulp'),
  uglify  = require('gulp-uglify'),
  concat  = require('gulp-concat'),
  less = require('gulp-less'),
  minifyCSS = require('gulp-clean-css'),
  del = require('del'),
  bower = require('gulp-bower');

// Minify js
var minifiedJs = [
  'bower_components/jquery/dist/jquery.min.js',
  'bower_components/bootstrap/dist/js/bootstrap.min.js',
  'bower_components/datatables/media/js/jquery.dataTables.min.js',
  'bower_components/datatables/media/js/dataTables.bootstrap.min.js',
  'bower_components/pnotify/dist/pnotify.js',
  'bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js',
  'bower_components/select2/select2.min.js',
  'bower_components/google-code-prettify/bin/prettify.min.js',
  'bower_components/fine-uploader/dist/fine-uploader.min.js'
];

var changingJS = [
  'bower_components/jquery-nestable/jquery.nestable.js',
  'js/admin.js'
];

gulp.task('packJS', function(){

  // concat already minified files
  gulp.src(minifiedJs)
  .pipe(concat('minified1.js'))
  .pipe(gulp.dest('./js/'))
  .on('end', function(){

    // uglify & concat  plain files
    gulp.src(changingJS)
    .pipe(concat('minified2.js'))
    .pipe(uglify())
    .pipe(gulp.dest('./js/'))
    .on('end', function(){

      // Concat temporary files
      gulp.src(['./js/minified1.js', './js/minified2.js'])
      .pipe(concat('admin.min.js'))
      .pipe(gulp.dest('./js/'))
      .on('end', function(){

        // Delete temporary files
        del(['./js/minified1.js', './js/minified2.js'])
        .then(function(){
          console.log('Javascript packed!');
        });
      });
    });
  });
});



gulp.task('frontCSS', function () {
  gulp.src('sites/default/css/styles.less')
  .pipe(less().on('error', function(err){ console.log(err.message); }))
  .pipe(minifyCSS())
  .pipe(gulp.dest('sites/default/css/'));
  //.pipe(reload({stream: true}));
});

gulp.task('adminCSS', function () {
  gulp.src('less/admin.less')
  .pipe(less().on('error', function(err){ console.log(err.message); }))
  .pipe(minifyCSS())
  .pipe(gulp.dest('css/'));
  //.pipe(reload({stream: true}));
});

gulp.task('bower', function(){
  bower().pipe(gulp.dest('bower_components/'))
  .on('end', function(){
    // preen.preen({});
    gulp.start('packJS');
    gulp.start('adminCSS');
  });
});


gulp.task('default', [], function(){
  gulp.watch(changingJS, ['packJS']);
  //  gulp.watch(['index.php', 'sites/default/**/*.twig'], [reload]);
  gulp.watch(['less/**/*.less', '!less/admin.less'], ['frontCSS', 'adminCSS']);
  gulp.watch(['sites/default/css/*.less'], ['frontCSS']);
  gulp.watch(['less/admin.less'], ['adminCSS']);
});
