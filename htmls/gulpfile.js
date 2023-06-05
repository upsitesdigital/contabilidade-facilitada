var gulp = require('gulp');
var $ = require('gulp-load-plugins')();
var browserSync = require('browser-sync').create();

var webpack = require('webpack');
var webpackConfig = require('./webpack.config.js');
var bundler = webpack(webpackConfig);

var uglify = require('gulp-uglify');

var isWatching = false;

/**
 * SVG
 */
gulp.task('svg2png', function() {
  var svg = gulp.src(['./src/img/svg/*.svg']);

  svg
    .pipe($.changed('./assets/img/'))
    .pipe($.svgmin())
    .pipe(gulp.dest('./assets/img/'));

  return svg;
});
/**
 * Icons
 */
gulp.task('svgicons', function() {
  var path = require('path');
  return gulp.src(['./src/img/icons/*.svg'])
    .pipe($.svgmin(function(file) {
      var prefix = path.basename(file.relative, path.extname(file.relative));
      return {
        plugins: [{
          removeAttrs: { attrs: 'fill' },
          cleanupIDs: {
            prefix: prefix + '-',
            minify: true
          }
        }]
      };
    }))
    .pipe($.svgstore())
    .pipe(gulp.dest('./assets/img/'));
});


/**
 * Sass
 */
gulp.task('sass', function() {
  return gulp.src(['./src/sass/main.scss'])
    .pipe($.sourcemaps.init())
    .pipe(
      $.sass({
        includePaths: ['./node_modules/'],
        outputStyle: 'expanded'
      })
      .on('error', $.sass.logError)
    )
    .pipe($.sourcemaps.write())
    .pipe(gulp.dest('./assets/css/'))
    .pipe(browserSync.stream());
});

gulp.task('sass:release', ['sass'], function() {
  return gulp.src(['./assets/css/main.css'])
    .pipe($.autoprefixer(['last 2 versions', 'ie 8', 'ie 9', '> 1%']))
    .pipe($.rename({ suffix: '.min' }))
    .pipe($.cleanCss({ compatibility: 'ie8' }))
    .pipe(gulp.dest('./assets/css/'));
});

gulp.task('js:release', function() {
  gulp.src(['./assets/js/bundle.js'])
    .pipe(uglify())
    .pipe($.rename({ suffix: '.min' }))
    .pipe(gulp.dest('./assets/js/')) // It will create folder client.min.js
});


/**
 * Javascript
 */
gulp.task('scripts', function(cb) {
  var handler = function(err, stats) {
    if (err) {
      throw new $.util.PluginError('webpack', err);
    }

    $.util.log('[webpack]', stats.toString('normal'));

    browserSync.reload(webpackConfig.output.filename);
  };

  if (isWatching) {
    bundler.watch(200, handler);
  } else {
    bundler.run(handler);
  }

  return cb();
});


/**
 * Live preview
 */
gulp.task('serve', function() {
  browserSync.init({
    // open: false,
    server: {
      // proxy: 'framework.local.com',
      baseDir: './'
    }

  });
});


/**
 * Build
 */
gulp.task('build', ['sass', 'scripts', 'svg2png'], function() {
  $.util.log($.util.colors.green('Build is finished'));
});

gulp.task('build:min', function() {
  gulp.start('sass:release', 'js:release');
});

/**
 * Watch files
 */
gulp.task('watch', ['serve'], function() {
  isWatching = true;

  /* Watch htmls */
  gulp.watch('./*.html', { cwd: './' }).on('change', browserSync.reload);

  /* Watch styles */
  gulp.watch(['**/*.scss'], { cwd: './src/sass/' }, ['sass']).on('change', browserSync.reload);

  /* Watch SVG */
  gulp.watch(['*.svg'], { cwd: './src/img/icons/' }, ['svgicons']).on('change', browserSync.reload);
  gulp.watch(['*.svg'], { cwd: './src/img/svg/' }, ['svg2png']).on('change', browserSync.reload);
});

/**
 * Default task
 */
gulp.task('default', ['watch', 'build', 'svgicons']);