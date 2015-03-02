<?php
require_once  __DIR__ . '/../registry.php';

use Symfony\Component\Yaml\Yaml;

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
    return fetch_shares(false);
});

$app->get('/shares/{query}', function($query) use ($app) {
    return fetch_shares($query);
});

$app->get('/shares/{username}/{query}', function($username, $query) use ($app) {
    return fetch_shares($username . '/' . $query);
});

function fetch_config($yoda_file){
    if(file_exists('../share/' . $yoda_file)) {
        return file_get_contents('../share/' . $yoda_file);
    } else {
        exit;
    }
}

function fetch_shares($to_search_for = false){
    $files = glob(dirname(__DIR__) . '/share/**/*');
    $shares = array();
    foreach($files as $file){
        if($file == '.' || $file == '..'){
            continue;
        }
        $contents = file_get_contents($file);
        $yoda = str_replace(dirname(__DIR__) . '/share/', '', $file);
        if($to_search_for && !stristr($yoda, $to_search_for)) {
            continue;
        }
        try {
            $yaml = Yaml::parse($contents);
        } catch (\Exception $e) {
            continue;
        }
        $shares[$yoda] = array(
            'name'=>$yoda,
            'raw'=>$contents,
            'yaml'=>$yaml,
            'hosted'=>$_SERVER['SERVER_NAME']
        );
        /* Try to fetch the description */
        foreach($yaml as $container_name=>$config) {
            if(isset($config['description'])) {
                $shares[$yoda]['description'] = $config['description'];
            }
        }
    }
    return json_encode($shares, JSON_PRETTY_PRINT);

}

$app->run();
