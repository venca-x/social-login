module.exports = function (grunt) {

    require('load-grunt-tasks')(grunt, {scope: 'devDependencies'});
    grunt.loadNpmTasks('grunt-shell');

    grunt.initConfig({
        shell: {
            composer_self_update: {
                command: 'composer self-update'
            },
            composer_install: {
                command: 'composer update'
            },
            composer_update: {
                command: 'composer update'
            },
            composer_update_prefer_lowest: {
                command: 'composer update --no-progress --prefer-dist --prefer-lowest --prefer-stable'
            },
            test: {
                command: 'vendor\\bin\\tester tests -s -p php'
            },
            installCodeSoft: {
                command: 'composer create-project nette/coding-standard nette-coding-standard',
                command: 'composer create-project nette/code-checker nette-code-checker'
            },
            netteCodeChecker: {
                command: 'php ..\\..\\nette-code-checker\\code-checker -d src -d tests --short-arrays --strict-types'
            },
            netteCodeCheckerFIX: {
                command: 'php ..\\..\\nette-code-checker\\code-checker -d src -d tests --short-arrays --strict-types --fix'
            },
            netteCodingStandard: {
                command: 'php ..\\..\\nette-coding-standard\\ecs check src tests --config ..\\..\\nette-coding-standard\\coding-standard-php71.yml'
            },
            netteCodingStandardFIX: {
                command: 'php ..\\..\\nette-coding-standard\\ecs check src tests --config ..\\..\\nette-coding-standard\\coding-standard-php71.yml --fix'
            }
        }
    });

    grunt.registerTask('installDependencies', ['shell:composer_self_update','shell:composer_install', 'shell:installCodeSoft']);
    grunt.registerTask('test', ['shell:test']);
    grunt.registerTask('netteCodeChecker', ['shell:netteCodeChecker']);
    grunt.registerTask('netteCodeCheckerFIX', ['shell:netteCodeCheckerFIX']);
    grunt.registerTask('netteCodingStandard', ['shell:netteCodingStandard']);
    grunt.registerTask('netteCodingStandardFIX', ['shell:netteCodingStandardFIX']);

};
