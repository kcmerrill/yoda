<?php
namespace kcmerrill;

class yoda {
    var $app;
    var $action;
    var $modifier;
    var $version = 0.01;
    var $args;

    function __construct($app, $action = false, $modifier = false, $args = array()) {
        $this->app = $app;
        $this->action = $action;
        $this->modifier = $modifier;
        $this->args = is_array($args) ? $args : array();
        try {
            $this->$action($modifier);
        } catch (\Exception $e) {
            $this->speak();
            $this->app['cli']->out('<green>[Yoda]</green> <red>' . $e->getMessage() . '</red>');
        }
    }

    function lift_from_seek($env = false) {
        $this->app['cli']->out('<green>[Yoda]</green> ' . getcwd() . '/.yoda');
        $this->lift($env, false);
    }

    function lift($env = false, $speak = true) {
        $config = $this->app['config']->configFileContents($env);
        if($speak) {
            $this->speak();
        }
        if(in_array('--force', $this->args) && is_file('.yoda.setup'))  {
            unlink('.yoda.setup');
        }
        $instructions = $this->app['instruct']->lift($config);
        $this->app['shell']->executeInstructions($instructions, $config, in_array('--loudly', $this->args));
        file_put_contents('.yoda.setup', date("F j, Y, g:i a"));
    }

    function seek() {
        $this->speak();
        $configs = $this->app['config']->seekConfigFiles(getcwd());
        foreach($configs as $config) {
            chdir(dirname($config));
            new yoda($this->app, 'lift_from_seek', $this->modifier, $this->args);
        }
    }
    function control() {
        $this->speak();
        $config = $this->app['config']->configFileContents();
        $config = end($config);
        $this->app['shell']->executeCommandForeground($this->app['docker']->exec($config['name']));

    }
    function summon() {
        $this->speak();
        $this->app['cli']->out('<green>[Yoda] </green> The name of your new project, what is, hmm?');
        $folder = readline();
        if(is_dir($folder) && !in_array('--force', $this->args)) {
            throw new \Exception($folder . ' exists! Use the force(--force) and try again, you should.  Yes, hmmm.');
        } else {
            @mkdir($folder, 0700, true);
            chdir($folder);
            $this->app['config']->saveConfigFile($this->modifier);
            new yoda($this->app, 'lift_from_seek', false , $this->args);
        }
    }
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
