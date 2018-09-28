var gulp = require('gulp');
var sass = require('gulp-sass');
var autoprefixer = require('gulp-autoprefixer');

var input = './assets/scss/**/*.scss';
var output = './public/css';

var sassOptions = {
    errLogToConsole: true,
    outputStyle: 'compressed'
};
var autoprefixerOptions = {
    browsers: ['last 2 versions', '> 5%', 'Firefox ESR']
};

gulp.task('sass', function(){
    return gulp
        .src(input)
        .pipe(sass(sassOptions).on('error', sass.logError))
        .pipe(autoprefixer(autoprefixerOptions))
        .pipe(gulp.dest(output));
});

gulp.task('watch', function(){
    return gulp
        .watch(input, ['sass'])
});

gulp.task('default', ['sass', 'watch']);