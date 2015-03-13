<?php

namespace kcmerrill\yoda;
use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Yaml\Dumper;

class yamlConfig {
    var $config_file = '.yoda';
    var $defaults = array(
        'build'=>false, // _do not_ build by default
        'pull'=>false, // _do not_ pull by default
        'remove'=>false, // _do not_ remove by default
        'd'=>true, // detached mode
        't'=>false,
        'i'=>false,
        //No steps required by default
        'prompt'=>array(),
        'prompt_password'=>array(),
        'setup'=>array(),
        'success'=>array(),
        'control'=>false,
        'update'=>array(),
        'require'=>array(),
        /* Setup a debug env, useful for interactive mode */
        'env'=>array(
            'debug'=>array(
                'd'=>false,
                't'=>true,
                'i'=>true,
                'entrypoint'=>'bash'
            )
        )
    );
    var $custom = array(
        'build','pull','remove','image','env','run','notes','prompt','prompt_password', 'success','setup','control','require','description','update'
    );

    var $force_remove = false;
    var $app;

    function __construct($app, $force_remove = false) {
        $this->app = $app;
        $this->force_remove = $force_remove;
    }

    function smartConfig(){
        //Make sure yoda can start anywhere beneath the master folder
        while(dirname(getcwd()) != '/' && !is_file('.yoda')) {
           $this->app['shell']->cd(dirname(getcwd()));
        }
    }

    function save($file, $contents) {
        $yaml = Dumper::Dump($contents);
        return file_put_contents($file, $yaml) ? true : false;
    }

    function configFileContents($env = false) {
        $config_file = getcwd() . DIRECTORY_SEPARATOR . $this->config_file;
        if(!is_file($config_file)) {
            throw new \Exception('I get my directions from the .yoda file and I, find it,  cannot.  Herh herh herh.');
        } else {
            $contents = file_get_contents($config_file);
        }
        return $this->setDefaultsAndEnv($this->parseYaml($contents), $env);
    }

    function fetchConfigFile($shared, $repos) {
        $repos = is_array($repos) ? $repos : array();
        foreach($repos as $repo) {
            $contents = file_get_contents('http://' . rtrim($repo,'/') . '/share/'. $shared);
            if(!empty($contents)) {
               return $contents;
            }
        }
        throw new \Exception('It would appear that the project you are summoning could be found not.  Yes, hmmm.');
    }

    function saveConfigFile($shared, $repos){
        $config_file = $this->fetchConfigFile($shared, $repos);
        file_put_contents('.yoda', $config_file);
    }

    function seekConfigFiles($cwd) {
        if(is_dir($cwd)){
            $Directory = new \RecursiveDirectoryIterator($cwd, \FilesystemIterator::SKIP_DOTS);
            $Iterator = new \RecursiveIteratorIterator($Directory);
            $Regex = new \RegexIterator($Iterator, '/\/\\.yoda$/i', \RecursiveRegexIterator::MATCH);
            return $Regex;
        } else {
            return array();
        }
    }

    function setDefaultsAndEnv($configuration, $env = false) {
        foreach($configuration as $container_name=>$container_config) {
            $container_config['name'] = isset($container_config['name']) ? $container_config['name'] : $container_name;
            $container_config['name'] = trim(`echo {$container_config['name']}`);
            if(!isset($container_config['image'])) {
                throw new \Exception('An image, ' . $container_name . '  must have!');
            }
            $container_config = array_merge($this->defaults, $container_config);
            if($this->force_remove) {
                $container_config['remove'] = true;
            }
            if(isset($container_config['env'][$env])) {
                $container_config = array_merge($container_config, $container_config['env'][$env]);
            }
            unset($container_config['env']);
            $container_config['run'] = array();
            foreach($container_config as $key=>$value) {
                if(!in_array($key, $this->custom)) {
                    $container_config['run'][$key] = $value;
                }
            }
            $configuration[$container_name] = $container_config;
        }
        return $configuration;
    }

    function parseYaml($yaml = false){
        try {
            return Yaml::parse($yaml);
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
