<?php
error_reporting(E_ALL);
require_once __DIR__.'/../vendor/autoload.php';
$app = new Silex\Application();

$app->get('/', function() use ($app) {
    return 'hello world';
});

$app->get('/share/{config}', function ($config) use ($app) {
    return fetch_config($config);
});

$app->get('/share/{group}/{config}', function ($group, $config) use ($app) {
    return fetch_config($group . '/' . $config);
});


function fetch_config($yoda_file){
    if(file_exists('../share/' . $yoda_file)) {
        return file_get_contents('../share/' . $yoda_file);
    } else {
        return 'Find the .yoda file you seek I cannot.';
    }
}

$app->run();
