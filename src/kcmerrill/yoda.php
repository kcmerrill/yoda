<?php
namespace kcmerrill;

class yoda {
    var $app;
    var $action;
    var $modifier;
    var $version = 0.01;
    var $args;
    var $spoke = false;
    var $lifted = array();
    var $summoning = false;
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

    function lift($env = false, $speak = true) {
        $original_location = getcwd();
        $this->app['yaml']->smartConfig();
        $config = $this->app['yaml']->configFileContents($env);
        $setup = is_file('.yoda.setup');
        if($speak) {
            $this->speak();
        }
        if(in_array('--force', $this->args) && $setup) {
            unlink('.yoda.setup');
        }
        foreach($config as $container_name=>$container_config) {
            $require = is_array($container_config['require']) ? $container_config['require'] : array($container_config['require']);
            $required_project_folder = false;
            foreach($require as $req) {
                chdir('../');
                try {
                    $this->summon($req);
                } catch(\Exception $e) {
                    $this->lift($env, false);
                }
                chdir($original_location);
            }
            if(in_array($container_config['name'], $this->lifted)) {
                unset($config[$container_name]);
                $this->app['cli']->out('<green>[Yoda]</green><white> ' . $container_config['name'] . ' already running ... </white>');
            } else {
                $this->lifted[] = $container_config['name'];
            }
        }
        $instructions = $this->app['instruct']->lift($config);
        $this->app['shell']->executeLiftInstructions($instructions, $config, in_array('--loudly', $this->args));
        touch('.yoda.setup');
    }

    function seek() {
        $this->speak();
        $configs = $this->app['yaml']->seekConfigFiles(getcwd());
        foreach($configs as $config) {
            $this->app['cli']->out('<green>[Yoda]</green> <white>Found ... ' . $config . '</white>');
            chdir(dirname($config));
            $this->lift($this->modifier, false, true);
        }
    }
    function control() {
        $this->speak();
        $this->app['yaml']->smartConfig();
        $config = $this->app['yaml']->configFileContents($this->modifier);
        $instructions = $this->app['instruct']->control($config, $this->modifier);
        $this->app['shell']->executeInstructions($instructions, true);
    }
    function summon($project_name) {
        $this->speak();
        $folder = $project_name;
        if(strpos($folder, '/') === FALSE) {
            throw new \Exception('Only summon things that are name followed by project, I can!  Yeesssssss. ' . PHP_EOL . 'Eg: yoda summon db/mysql');
        } else {
            list($user, $folder) = explode('/', $folder, 2);
            $this->summoning = $folder;
        }
        if(is_dir($folder) && !in_array('--force', $this->args)) {
            chdir(getcwd() . '/' . $folder);
            $this->lift($project_name, false);
        } else {
            if(!is_file($folder)) {
                @mkdir($folder, 0755, true);
            }
            $repos = $this->app['config']->get('yoda.repos', array('yoda.kcmerrill.com'));
            chdir(getcwd() . '/' . $folder);
            $this->app['yaml']->saveConfigFile($project_name, $repos);
            $this->lift($project_name, false);
        }
        return $folder;
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
