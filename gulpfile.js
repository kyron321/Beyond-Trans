const { src, dest, watch, series, parallel } = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const autoprefixer = require('gulp-autoprefixer');
const sourcemaps = require('gulp-sourcemaps');
const uglify = require('gulp-uglify');
const del = require('del');
const rename = require('gulp-rename'); // Add gulp-rename

// File paths
const themeDir = 'wp-content/themes/beyond-trans';
const paths = {
  styles: {
    src: `${themeDir}/assets/scss/style.scss`,
    watch: `${themeDir}/assets/scss/**/*.scss`,
    dest: `${themeDir}/dist/css/`
  },
  adminStyles: {
    src: `${themeDir}/assets/scss/admin.scss`,
    watch: `${themeDir}/assets/scss/admin/**/*.scss`,
    dest: `${themeDir}/dist/css/`
  },
  scripts: {
    src: `${themeDir}/assets/js/**/*.js`,
    dest: `${themeDir}/dist/js/`
  }
};

// Clean dist directory
function clean() {
  return del([
    `${themeDir}/dist/css/**/*`,
    `${themeDir}/dist/js/**/*`
  ]);
}

// Main styles task
function styles() {
  return src(paths.styles.src)
    .pipe(sourcemaps.init())
    .pipe(sass.sync({ 
      outputStyle: 'compressed',
      includePaths: ['node_modules']
    }).on('error', sass.logError))
    .pipe(autoprefixer())
    .pipe(rename({ suffix: '.min' })) // Rename to .min.css
    .pipe(sourcemaps.write('./'))
    .pipe(dest(paths.styles.dest));
}

// Admin styles task
function adminStyles() {
  return src(paths.adminStyles.src)
    .pipe(sourcemaps.init())
    .pipe(sass.sync({ 
      outputStyle: 'compressed',
      includePaths: ['node_modules']
    }).on('error', sass.logError))
    .pipe(autoprefixer())
    .pipe(rename({ suffix: '.min' })) // Rename to .min.css
    .pipe(sourcemaps.write('./'))
    .pipe(dest(paths.adminStyles.dest));
}

// Scripts task
function scripts() {
  return src(paths.scripts.src)
    .pipe(sourcemaps.init())
    .pipe(uglify())
    .pipe(rename({ suffix: '.min' })) // Rename to .min.js
    .pipe(sourcemaps.write('./'))
    .pipe(dest(paths.scripts.dest));
}

// Watch files
function watchFiles() {
  watch(paths.styles.watch, styles);
  watch(paths.adminStyles.watch, adminStyles);
  watch(paths.scripts.src, scripts);
}

// Export tasks
exports.clean = clean;
exports.styles = styles;
exports.adminStyles = adminStyles;
exports.scripts = scripts;
exports.watch = watchFiles;

// Default task
exports.default = series(
  clean,
  parallel(styles, adminStyles, scripts)
);

// Build task
exports.build = series(
  clean,
  parallel(styles, adminStyles, scripts)
);