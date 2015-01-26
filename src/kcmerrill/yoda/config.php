<?php

namespace kcmerrill\yoda;
use Symfony\Component\Yaml\Yaml;

class config {
    var $config_file = '.yoda';
    var $defaults = array(
        'build'=>false, // _do not_ build by default
        'pull'=>false, // _do not_ pull by default
        'remove'=>false, // _do not_ remove by default
        'd'=>true // detached mode
    );
    var $custom = array(
        'build','pull','remove','image','env','run','notes','prompt'
    );

    function configFileContents($env = false) {
        $config_file = getcwd() . DIRECTORY_SEPARATOR . $this->config_file;
        if(!is_file($config_file)) {
            throw new \Exception('I get my directions from the .yoda file and I, find it,  cannot.  Herh herh herh.');
        } else {
            $contents = file_get_contents($config_file);
        }
        return $this->setDefaultsAndEnv($this->parseYaml($contents), $env);
    }

    function fetchConfigFile($shared) {
        $contents = file_get_contents('http://yoda.kcmerrill.com/share/'. $shared);
        if(!$contents) {
            throw new \Exception('Find the .yoda file you seek I cannot.');
        }
    }

    function setDefaultsAndEnv($configuration, $env = false) {
        foreach($configuration as $container_name=>$container_config) {
            $container_config['name'] = $container_name;
            if(!isset($container_config['image'])) {
                throw new \Exception('An image, ' . $container_name . '  must have!');
            }
            $container_config = array_merge($this->defaults, $container_config);
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
