<?php

require __DIR__ . '/vendor/autoload.php';

//TODO: Before committing, uncomment this line
error_reporting(0);

if (!ini_get('date.timezone')){
    date_default_timezone_set('America/Denver');
}

use Pimple\Container;

$app = new Container();

$app['docker'] = function ($c) {
    return new kcmerrill\yoda\docker;
};

$app['events'] = function ($c) {
    return new kcmerrill\utility\events;
};

$app['config'] = function($c) {
    $config = new kcmerrill\utility\config(__DIR__, true);
    $config['yoda.root'] = __DIR__;
    return $config;
};

$app['cli'] = function($c) {
    return new League\CLImate\CLImate;
};

$app['yaml'] = function($c) use ($argv){
    return new kcmerrill\yoda\yamlConfig(in_array('--force', $argv));
};

$app['instruct'] = $app->factory(function($c) {
    return new kcmerrill\yoda\instruct($c['docker']);
});

$app['shell'] = $app->factory(function($c) {
    return new kcmerrill\yoda\shell($c['cli']);
});

$app['yoda'] = function($c) use($argv) {
    return new kcmerrill\yoda($c, isset($argv[1]) ? $argv[1] : 'version', isset($argv[2]) ? $argv[2] : false, $argv);
};
