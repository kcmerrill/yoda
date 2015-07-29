<?php
namespace kcmerrill;

class yoda {
    var $app;
    var $action;
    var $modifier;
    var $version = 0.0;
    var $args;
    var $spoke = false;
    var $lifted = array();
    var $summoned = array();
    var $summoning = false;

    function __construct($app, $action = false, $modifier = false, $args = array()) {
        $this->app = $app;
        $this->action = str_replace('-','_',$action);
        $this->modifier = $modifier;
        $this->args = is_array($args) ? $args : array();
        /* Enable config speak option here */
        $this->spoke = $this->app['config']->get('yoda.speak', 'on') == 'on' ? false : true;
        $this->speak();
        /* Do we need to run the updater? */
        $this->app['updater']->check() ? $this->self_update() : NULL;
        /* Giddy Up! */
        try {
            $this->{$this->action}($modifier);
            if(in_array('--export', $this->args)) {
                $this->export($modifier);
            }
         } catch (\Exception $e) {
            $this->app['cli']->out('<green>[Yoda]</green> <red>' . $e->getMessage() . '</red>');
         }

    }

    function edit() {
        $this->app['run_config']->smartConfig();
        $this->app['shell']->execute('$EDITOR .yoda', true, false, true);
    }

    function repos() {
        $repos = $this->app['repos']->get(false, true);
        foreach($repos as $repo=>$running) {
            $this->app['cli']->out('<green>[Yoda]</green> <' . ($running ? 'white' : 'red') . '>' . $repo . '</' . ($running ? 'white' : 'red') . '>');
        }
    }

    function join($repo = false) {
        return $this->add($repo);
    }

    function add($repo = false) {
        return $this->add_repo($repo);
    }

    function add_repo($repo = false) {
        if($repo) {
            //TODO:: Move yaml save to the configuration class
            if($this->app['repos']->add($repo)) {
                $this->app['cli']->out('<green>[Yoda]</green> <white>Added repository "'. $repo .'".</white>');
            }
        } else {
            throw new \Exception('Have, a valid URL I must.' . PHP_EOL . '<white>Eg: yoda add-repo http://yoda.yourawesomesitehere.com</white>');
        }
    }

    function leave($repo = false) {
        return $this->remove_repo($repo);
    }

    function remove_repo($repo = false) {
        if($repo) {
            if($this->app['repos']->remove($repo)) {
                $this->app['cli']->out('<green>[Yoda]</green> <white>Removed repository "'. $repo .'".</white>');
            }
        } else {
            throw new \Exception('Have, a valid URL I must.' . PHP_EOL . '<white>Eg: yoda remove-repo http://yoda.yourawesomesitehere.com</white>');
        }
    }

    function self_update($env = false) {
        $cwd = getcwd();
        $root_dir = $this->app['config']->c('yoda.system.root_dir');
        $this->app['shell']->cd($root_dir);
        $this->app['cli']->out('<green>[Yoda]</green> <white>To update I need. herh.</white>');
        $this->app['shell']->execute('git pull', in_array('--loudly', $this->args));
        $this->app['shell']->execute('docker run -v $PWD:/app composer/composer update', in_array('--loudly', $this->args));
        $this->app['shell']->execute('docker run -v $PWD/www:/app composer/composer update', in_array('--loudly', $this->args));
        $this->app['shell']->cd($cwd);
        touch($root_dir . '/yoda.last_updated');
    }

    function share($share_as = false) {
        $this->app['run_config']->smartConfig();
        $root_dir = $this->app['config']->c('yoda.system.root_dir');
        $new_share = $root_dir . '/www/share/' . $share_as;
        if($share_as) {
            if(in_array('--force', $this->args) || !is_file($new_share)) {
                try {
                    mkdir(dirname($new_share), 0755, true);
                }
                catch(\Exception $e) {}
                if(is_file('.yoda')) {
                    file_put_contents($new_share, file_get_contents('.yoda'));
                    $this->app['cli']->out('<green>[Yoda]</green> <white>Shared your wisdom with the world, I have.  Hmmmmmm.</white>');
                } else {
                    throw new \Exception('Have, a valid .yoda file I must.');
                }
            } else {
                throw new \Exception($share_as . ' exists! Use the force(--force) and try again, you should.  Yes, hmmm.');
            }
        } else {
            throw new \Exception('Only share things that are name followed by project, I can!  Yeesssssss. ' . PHP_EOL . 'Eg: yoda share db/mysql');
        }
    }

    function update($env = false) {
        $this->app['run_config']->smartConfig();
        $config = $this->app['run_config']->configFileContents($env);
        foreach($config as $container_name=>$container_config) {
            $update = is_array($container_config['update']) ? $container_config['update'] : array($container_config['update']);
            foreach($update as $command) {
                $this->app['shell']->execute($command, in_array('--loudly', $this->args));
            }
        }
        /* Perform a lift to get the updated changes */
        $this->lift($env);
    }

    function search($to_find = false) {
        $this->find($to_find);
    }

    function find($to_find = false, $display = true) {
        $repos = $this->app['repos']->get(true);
        $shares = $this->app['shares']->get($repos, $to_find);
        foreach($shares as $share_name=>$share_data) {
            $description = isset($share_data['description']) ? $share_data['description'] : 'No description available';
            $hosted = isset($share_data['hosted']) ? $share_data['hosted'] : 'unknown';
            $this->app['cli']->out('<green>'. str_pad($share_name, 25, ' ') .'</green> - <white>'. $description .'</white>');
            $this->app['cli']->out('<light_blue>' . str_pad($hosted, 28 + strlen($hosted), ' ', STR_PAD_LEFT) . '</light_blue>');
            echo PHP_EOL;
        }
    }

    function config($config) {
        if(isset($this->args[2]) && isset($this->args[3])) {
            /* meaning we should set the config */
            $this->app['config']->set('yoda.' . $this->args[2], $this->args[3]);
            if($this->app['config']->save('yoda', $this->app['config']->c('yoda.system.root_dir') . DIRECTORY_SEPARATOR . $this->app['config']->c('yoda.system.config_name'))) {
                $this->app['cli']->out('<green>[Yoda]</green> <white>Set '. $this->args[2] .' to ' . $this->args[3] .'</white>');
            } else {
                throw new \Exception('Save your settings I cannot.');
            }
        } elseif(isset($this->args[2])) {
            $this->app['cli']->out('<green>[Yoda]</green> <white>' . $this->args[2] . ' is set to ' . $this->app['config']->get('yoda.' . $this->args[2], '<red>n/a</red>') . '</white>');
        } else {
          throw new \Exception('Please specify a config to display');
        }
    }


    function export($env = false) {
        /* ToDo: Would be nice to export env name(but due to args, this will have to come later) */
        $file_to_write = $this->app['config']->c('yoda.system.initial_working_dir') . DIRECTORY_SEPARATOR . 'yoda.sh';
        /* Don't actually run the commands */
        $this->app['shell']->dryRun(true);
        /* if someone calls this without a param only */
        if(count($this->app['shell']->commands()) == 0){
            $this->lift($env);
        }
        if(file_put_contents($file_to_write, implode("\n", $this->app['shell']->commands()))){
            chmod($file_to_write, 0755);
            $this->app['cli']->out('<green>[Yoda]</green> <white>Shared my wisdom with a shell script, I have.  Hmmmmmm.</white>');
        } else {
            throw new \Exception('Having problems writing my wisdom to the appropriate file I am.  Yes, hmmm.');
        }
    }

    function restart($env = false) {
        $this->lift();
    }

    function lift($env = false) {
        $this->app['run_config']->smartConfig();
        $original_location = getcwd();
        $config = $this->app['run_config']->configFileContents($env);
        $setup = is_file('.yoda.setup');
        $to_lift = array();
        if(in_array('--force', $this->args) && $setup) {
            unlink('.yoda.setup');
        }

        if (count($config) > 1) {
            $this->app['cli']->out('<green>[Yoda]</green><white> This project has ' . count($config) . " containers:\n       - " . implode("\n       - ", array_keys($config)) . '<white>' );
        }

        foreach($config as $container_name=>$container_config) {
            $this->app['cli']->out('<green>[Yoda] lifting </green><white>' . $container_name . ' ... </white>');

            $require = is_array($container_config['require']) ? $container_config['require'] : array($container_config['require']);

            if (count($require) > 0) {
                $this->app['cli']->out("<green>[Yoda]</green> <white>$container_name depends on:\n       - " . implode("\n       - ", $require) . ' </white>');
            }

            $required_project_folder = false;
            foreach($require as $req) {
                $this->app['shell']->cd('../');
                try {
                    $this->summon($req);
                } catch(\Exception $e) {
                    $this->lift($env);
                }
                $this->app['shell']->cd($original_location);
            }
            if(in_array($container_config['name'], $this->lifted)) {
                unset($config[$container_name]);
                $this->app['cli']->out('<yellow>[Yoda]</yellow> ' . $container_config['name'] . ' already running');
            } else {
                $this->lifted[] = $container_config['name'];
            }
            // Any additional lifts we would need?
            if(isset($container_config['lift']) && $container_config['lift']){
                $to_lift = is_string($container_config['lift']) ? array($container_config['lift']) : $container_config['lift'];
            }
        }
        $instructions = $this->app['instruct']->lift($config);
        $this->app['shell']->executeLiftInstructions($instructions, $config, in_array('--loudly', $this->args));
        foreach($to_lift as $env_to_lift) {
            $this->app['cli']->out('<green>[Yoda]</green><white> Lifting now with </white><green> ' . $env_to_lift . '</green>');
            $this->lift($env_to_lift);
        }
        $this->app['cli']->out("<green>[Yoda] lift </green><white>" . implode(', ', array_keys($config)) . " done.</white>");
        touch('.yoda.setup');
    }

    function seek() {
        $configs = $this->app['run_config']->seekConfigFiles(getcwd());
        foreach($configs as $config) {
            $this->app['cli']->out('<green>[Yoda]</green> <white>Found ... ' . $config . '</white>');
            $this->app['shell']->cd(dirname($config));
            if(in_array('--update', $this->args)) {
                $this->update($this->modifier);
            } else {
                $this->lift($this->modifier);
            }
        }
    }

    function control($modifier) {
        $this->app['run_config']->smartConfig();
        $config = $this->app['run_config']->configFileContents($modifier);
        $instructions = $this->app['instruct']->control($config, $modifier);
        $this->app['shell']->executeInstructions($instructions, true);
    }

    function pull($project_name) {
        return $this->init($project_name);
    }
    function init($project_name) {
        $repos = $this->app['repos']->get();
        $this->app['run_config']->saveConfigFile($project_name, $repos);
        $this->lift();
    }

    function summon_all($to_find = false) {
        $this->app['cli']->out('<green>[Yoda]</green> <white>Summoning all ' . ($to_find ? $to_find . ' ' : '') . '...</white>');

        $repos = $this->app['repos']->get(true);
        $shares = $this->app['shares']->get($repos, $to_find);
        foreach($shares as $share_name=>$share_config) {
            $this->app['cli']->out('<green>[Yoda]</green> <white>Found the project "'. $share_name .'" you seek, I have.  Hmmmmmm.</white>');
            $this->summon($share_name, true);
        }
    }

    function summon($project_name, $skip_all = false) {
        if(!$skip_all && in_array('--all', $this->args)) {
            return $this->summon_all($project_name);
        }

        if(strpos($project_name, '/') === FALSE) {
            throw new \Exception('Only summon things that are name followed by project, I can!  Yeesssssss. ' . PHP_EOL . 'Eg: yoda summon db/mysql');
        }

        list($user, $folder) = explode('/', $project_name, 2);
        $this->summoning = $folder;

        if(in_array($project_name, $this->summoned)) {
            $this->app['cli']->out("<yellow>[Yoda]</yellow> $project_name has already been summoned");
            return $folder;
        }

        // Does that project already exist?
        if(is_dir($folder) && !in_array('--force', $this->args)) {

            $this->app['cli']->out("<green>[Yoda]</green> Found $project_name");

            // change into that projects dir and lift
            $this->app['shell']->cd(getcwd() . '/' . $folder);
            $this->lift($project_name);
        } else {

            $this->app['cli']->out("<green>[Yoda] summon </green><white>$project_name ... </white>");

            // create a folder for the project and lift it
            if(!is_file($folder)) {
                @mkdir($folder, 0755, true);
            }
            $repos = $this->app['repos']->get();
            $this->app['shell']->cd(getcwd() . '/' . $folder);
            $this->app['run_config']->saveConfigFile($project_name, $repos);
            $this->lift($project_name);
        }

        $this->app['cli']->out("<green>[Yoda] summon </green><white>$project_name done.</white>");
        $this->summoned[] = $project_name;

        return $folder;
    }

    function inspect($project_name) {
        $repos = $this->app['repos']->get();
        $config_file = $this->app['run_config']->fetchConfigFile($project_name, $repos);
        echo $config_file;
    }

    function diff($project_name) {
        $this->app['run_config']->smartConfig();
        $repos = $this->app['repos']->get();
        $config_file = $this->app['run_config']->fetchConfigFile($project_name, $repos);
        $this->app['utility']->diff(file_get_contents('.yoda'), $config_file);
    }

    function version($modifier = false) {
        chdir($this->app['config']->c('yoda.system.root_dir'));
        $this->app['cli']->out('v' . "{$this->version}.0" . `git shortlog | grep -E '^[ ]+\w+' | wc -l`);
        echo PHP_EOL;
        $this->app['cli']->out('For help, please see <green>http://yoda.kcmerrill.com</green>');
    }

    function kill($modifier = false) {
        $this->app['shell']->execute($this->app['docker']->killall(), in_array('--loudly', $this->args));
    }

    function clean($modifier = false) {
        $this->app['shell']->execute($this->app['docker']->cleanDangling(in_array('--force',$this->args)), in_array('--loudly', $this->args), false, true);
        if(in_array('--exited', $this->args)) {
            $this->app['shell']->execute($this->app['docker']->cleanExited(in_array('--force',$this->args)), in_array('--loudly', $this->args), false, true);
        }
    }

    function speak() {
        if($this->spoke) {
            return true;
        }
$this->app['cli']->out("
           <green>.--.</green>
   <green>\`--._,'.::.`._.--'/</green>       <green>[Do]</green> <white>||</white> <red>[*Did Not]</red>
     <green>.  ` __::__ '  .</green>          <white>There is </white>!<yellow>[Try]</yellow>
       <green>- .`'..`'. -</green>
         <green>\ `--' /</green>                      <white>-</white><green>Yoda</green>\n");

        $this->spoke = true;
    }

    function __call($method, $params) {
        $this->app['cli']->out('<green>[Yoda]</green> <white>Attempt to control <underline>' . $method . '</underline> I will</white>');
        $this->control($method);
    }
}
