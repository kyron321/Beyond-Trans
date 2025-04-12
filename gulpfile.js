const { src, dest, watch, series, parallel } = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const autoprefixer = require('gulp-autoprefixer');
const uglify = require('gulp-uglify');
const { deleteAsync } = require('del');
const rename = require('gulp-rename');

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
  },
  blocks: {
    src: `${themeDir}/blocks/*/*.scss`,
    watch: `${themeDir}/blocks/*/*.scss`
  },
  images: {
    src: `${themeDir}/assets/images/**/*`,
    watch: `${themeDir}/assets/images/**/*`,
    dest: `${themeDir}/dist/images/`
  }
};

// Clean dist directory
async function clean() {
  return await deleteAsync([
    `${themeDir}/dist/css/**/*`,
    `${themeDir}/dist/js/**/*`,
    `${themeDir}/dist/images/**/*`  // Add images to clean task
  ]);
}

// Main styles task
function styles() {
  return src(paths.styles.src)
    .pipe(sass.sync({ 
      outputStyle: 'compressed',
      includePaths: ['node_modules']
    }).on('error', sass.logError))
    .pipe(autoprefixer())
    .pipe(rename({ suffix: '.min' }))
    .pipe(dest(paths.styles.dest));
}

// Admin styles task
function adminStyles() {
  return src(paths.adminStyles.src)
    .pipe(sass.sync({ 
      outputStyle: 'compressed',
      includePaths: ['node_modules']
    }).on('error', sass.logError))
    .pipe(autoprefixer())
    .pipe(rename({ suffix: '.min' }))
    .pipe(dest(paths.adminStyles.dest));
}

// Blocks styles task
function blockStyles() {
  return src(paths.blocks.src)
    .pipe(sass.sync({ 
      outputStyle: 'compressed',
      includePaths: ['node_modules']
    }).on('error', sass.logError))
    .pipe(autoprefixer())
    .pipe(rename(function(path) {
      path.extname = '.css';
    }))
    .pipe(dest(function(file) {
      return file.base;
    }));
}

// Scripts task
function scripts() {
  return src(paths.scripts.src)
    .pipe(uglify())
    .pipe(rename({ suffix: '.min' }))
    .pipe(dest(paths.scripts.dest));
}

// Images task
function images() {
  return src(paths.images.src)
    .pipe(dest(paths.images.dest));
}

// Watch files
function watchFiles() {
  watch(paths.styles.watch, styles);
  watch(paths.adminStyles.watch, adminStyles);
  watch(paths.scripts.src, scripts);
  watch(paths.blocks.watch, series(blockStyles, styles, scripts));
  watch(paths.images.watch, images);
}

// Export tasks
exports.clean = clean;
exports.styles = styles;
exports.adminStyles = adminStyles;
exports.blockStyles = blockStyles;
exports.scripts = scripts;
exports.images = images;
exports.watch = watchFiles;

// Default task
exports.default = series(
  clean,
  parallel(styles, adminStyles, blockStyles, scripts, images)
);

// Build task
exports.build = series(
  clean,
  parallel(styles, adminStyles, blockStyles, scripts, images)
);