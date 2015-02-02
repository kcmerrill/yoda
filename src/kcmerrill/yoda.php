<?php
namespace kcmerrill;

class yoda {
    var $app;
    var $action;
    var $modifier;
    var $version = 0.01;
    var $args;
    var $spoke = false;

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
        $this->app['config']->smartConfig();
        $config = $this->app['config']->configFileContents($env);
        $setup = is_file('.yoda.setup');
        if($speak) {
            $this->speak();
        }
        if(in_array('--force', $this->args) && $setup)  {
            unlink('.yoda.setup');
        }
        $instructions = $this->app['instruct']->lift($config);
        $this->app['shell']->executeLiftInstructions($instructions, $config, in_array('--loudly', $this->args));
        touch('.yoda.setup');
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
        $this->app['config']->smartConfig();
        $config = $this->app['config']->configFileContents();
        $instructions = $this->app['instruct']->control($config, $this->modifier);
        $this->app['shell']->executeInstructions($instructions, true);
    }
    function summon() {
        $this->speak();
        $this->app['cli']->out('<green>[Yoda] </green> The name of your new project, what is, hmm?');
        $folder = readline();
        if(is_dir($folder) && !in_array('--force', $this->args)) {
            throw new \Exception($folder . ' exists! Use the force(--force) and try again, you should.  Yes, hmmm.');
        } else {
            if(!is_file($folder)) {
                mkdir($folder, 0700, true);
            }
            chdir($folder);
            $this->app['config']->saveConfigFile($this->modifier);
            new yoda($this->app, 'lift_from_seek', false , $this->args);
        }
    }
    function version($modifier = false) {
        $this->speak();
        $this->app['cli']->out('v' . $this->version);
    }

    function kill($modifier = false) {
        $this->speak();
        $this->app['shell']->execute($this->app['docker']->killall(), in_array('--loudly', $this->args));
    }
    function speak() {
        if($this->spoke) {
            return true;
        }
$this->app['cli']->out("
           <green>.--.</green>
   <green>\`--._,'.::.`._.--'/</green>       <green>[Do]</green> <white>||</white> <red>[Do Not]</red>
     <green>.  ` __::__ '  .</green>          <white>There is </white>!<yellow>[Try]</yellow>
       <green>- .`'..`'. -</green>
         <green>\ `--' /</green>                      <white>-</white><green>Yoda</green>\n");

        $this->spoke = true;
    }
    function __call($method, $params) {
        throw new \Exception($method . '? I know not what you mean.');
    }
}
