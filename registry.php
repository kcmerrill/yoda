<?php

require __DIR__ . '/vendor/autoload.php';

//error_reporting(0);

use Pimple\Container;

$app = new Container();

$app['cwd'] = getcwd();

$app['docker'] = function ($c) {
    return new kcmerrill\yoda\docker;
};

$app['events'] = function ($c) {
    return new kcmerrill\utility\events;
};

$app['config'] = function($c) {
    return new kcmerrill\utility\config;
};

$app['cli'] = function($c) {
    return new League\CLImate\CLImate;
};

$app['config'] = function($c) {
    return new kcmerrill\yoda\config;
};

$app['instruct'] = $app->factory(function($c) {
    return new kcmerrill\yoda\instruct($c['docker']);
});

$app['shell'] = $app->factory(function($c) {
    return new kcmerrill\yoda\shell($c['cli']);
});

$app['yoda'] = function($c) use($argv) {
    return new kcmerrill\yoda($c, $argv[1], $argv[2]);
};
