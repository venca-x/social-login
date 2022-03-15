var gulp = require('gulp');
var shell = require('gulp-shell');

gulp.task('shell_npm_update', shell.task('npm update'));

gulp.task('shell_composer_self_update', shell.task('composer self-update'));
gulp.task('shell_composer_update', shell.task('composer update'));
gulp.task('shell_composer_update_prefer_lowest', shell.task('composer update --no-progress --prefer-dist --prefer-lowest --prefer-stable'));
gulp.task('shell_test', shell.task('vendor\\bin\\tester tests -s -p php'));

gulp.task('shell_create_project_coding_standard', shell.task('composer create-project nette/coding-standard nette-coding-standard'));
gulp.task('shell_create_project_coding_checker', shell.task('composer create-project nette/code-checker nette-code-checker'));

gulp.task('shell_netteCodeChecker', shell.task('php .\\nette-code-checker\\code-checker -d src --short-arrays --strict-types'));
gulp.task('shell_netteCodeCheckerFIX', shell.task('php .\\nette-code-checker\\code-checker -d src --short-arrays --strict-types --fix'));
gulp.task('shell_netteCodingStandard', shell.task('php .\\nette-coding-standard\\ecs check src tests --preset php74'));
gulp.task('shell_netteCodingStandardFIX', shell.task('php .\\nette-coding-standard\\ecs check src tests --preset php74 --fix'));


gulp.task('default', gulp.series('shell_npm_update', 'shell_composer_self_update', 'shell_composer_update'));
gulp.task('installDependencies', gulp.series('shell_npm_update', 'shell_composer_self_update'));
gulp.task('test', gulp.series(['shell_test']));
