<?php
require_once __DIR__ . '/../registry.php';

$app->get('/', function() use ($app) {
    return file_get_contents('views/index.html');
});

$app->get('/share/{config}', function ($config) use ($app) {
    return fetch_config($config);
});

$app->get('/share/{group}/{config}', function ($group, $config) use ($app) {
    return fetch_config($group . '/' . $config);
});

$app->get('/shares/', function() use ($app) {
    $files = glob(dirname(__DIR__) . '/share/**/*');
    var_dump($files);die();
    $shares = array();
    foreach($files as $file){
        if($file == '.' || $file == '..'){
            continue;
        }
        file_get_contents(dirname(__DIR__) . '/share/' . 
    }
    return $shares;
});

function fetch_config($yoda_file){
    if(file_exists('../share/' . $yoda_file)) {
        return file_get_contents('../share/' . $yoda_file);
    } else {
        exit;
    }
}

$app->run();
