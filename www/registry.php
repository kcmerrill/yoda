<?php
error_reporting(E_ALL);
require_once __DIR__ . '/vendor/autoload.php';

$app = new Silex\Application();

$app['db'] = function ($c) {
    $capsule = new Capsule;
    $capsule->addConnection(array(
        'driver'    => 'mysql',
        'host'      => 'mysql',
        'database'  => 'yoda',
        'username'  => 'root',
        'password'  => 'YODA',
        'charset'   => 'utf8',
        'collation' => 'utf8_unicode_ci',
        'prefix'    => '',
    ));
    $capsule->bootEloquent();
    $capsule->schema()->create('users', function($table) {
        $table->increments('id');
        $table->string('email')->unique();
        $table->timestamps();
    });
    return $capsule;
};
