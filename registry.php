<?php
require __DIR__ . '/vendor/autoload.php';

//TODO: Before committing, uncomment this line
error_reporting(0);

if (!ini_get('date.timezone')){
    date_default_timezone_set('America/Denver');
}

use Pimple\Container;

$app = new Container();

$app['utility'] = function ($c) use ($app) {
    return new kcmerrill\yoda\utility;
};

$app['updater'] = function ($c) use ($app) {
    return new kcmerrill\yoda\updater($app['config']);
};

$app['shares'] = function ($c) use ($app) {
    return new kcmerrill\yoda\shares($app['config']);
};

$app['repos'] = function ($c) use ($app) {
    return new kcmerrill\yoda\repos($app['config']);
};

$app['docker'] = function ($c) {
    return new kcmerrill\yoda\docker;
};

$app['events'] = function ($c) {
    return new kcmerrill\utility\events;
};

$app['config'] = function($c) {
    $config = new kcmerrill\utility\config(__DIR__, true);
    $config['yoda.system.root_dir'] = __DIR__;
    $config['yoda.system.initial_working_dir'] = getcwd();
    $config['yoda.system.config_name'] = 'yoda.config';
    $config['yoda.speak'] = $config->get('yoda.speak', 'on');
    $config['yoda.args.loudly'] = $config->get('yoda.args.loudly', 'off');
    return $config;
};

$app['argv'] = function($c) use ($argv) {
    if(count($argv) == 1) {
        /* yoda command only(should display version) */
        return $argv;
    }
    $yoda_default_args = $c['config']->get('yoda.args', array());
    foreach($yoda_default_args as $a=>$v) {
        if(strtolower($v) == 'on') {
            $argv[] = '--' . $a;
        }
    }
    return $argv;
};

$app['cli'] = function($c) {
    return new League\CLImate\CLImate;
};

$app['run_config'] = function($c) use ($app){
    return new kcmerrill\yoda\runConfig($app, in_array('--force', $c['argv']));
};

$app['instruct'] = $app->factory(function($c) {
    return new kcmerrill\yoda\instruct($c['docker'], $c['argv']);
});

$app['shell'] = function($c) {
    return new kcmerrill\yoda\shell($c['cli'], $c['docker']);
};

$app['yoda'] = function($c) {
    $argv = $c['argv'];
    return new kcmerrill\yoda($c, isset($argv[1]) ? $argv[1] : 'version', isset($argv[2]) ? $argv[2] : false, $argv);
};
