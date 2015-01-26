<?php

namespace kcmerrill;

class yoda {
    var $app;
    var $action;
    var $modifier;
    var $version = 0.01;

    function __construct($app, $action = false, $modifier = false) {
        $this->app = $app;
        $this->action = $action;
        $this->modifier = $modifier;
        try {
            $this->$action($modifier);
        } catch (\Exception $e) {
            $this->app['cli']->red($e->getMessage());
        }
    }

    function lift($env = false) {
        $this->speak();
        $config = $this->app['config']->configFileContents($env);
        $instructions = $this->app['instruct']->lift($config);
        $this->app['shell']->executeInstructions($instructions, $config);
   }
    function seek() {}
    function command() {}
    function version($modifier = false) {
        $this->speak();
        $this->app['cli']->out('v' . $this->version);
    }
    function speak() {
$this->app['cli']->out("
           <green>.--.</green>
   <green>\`--._,'.::.`._.--'/</green>       <white>Do or do not.</white>
     <green>.  ` __::__ '  .</green>        <white>There is no try.</white>
       <green>- .`'..`'. -</green>
         <green>\ `--' /</green>                      -<green>Yoda</green>\n");

    }
    function __call($method, $params) {
        throw new \Exception($method . '? I know not what you mean.');
    }
}
