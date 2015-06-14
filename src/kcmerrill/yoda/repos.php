<?php
namespace kcmerrill\yoda;

class repos {
    var $config;
    function __construct($config) {
        $this->config = $config;
    }

    function get($reverse = false) {
        $repos = $this->config->get('yoda.repos', array());
        array_unshift($repos, 'yoda.kcmerrill.com');
        array_unshift($repos, 'yoda.' . gethostname());
        $repos = array_unique($repos);
        return $reverse ?  array_reverse($repos) : $repos;
    }

    function add($repo, $yaml, $config) {
        $repo = str_replace(array('http://','www.'), '', $repo);
        $yoda = $config->c('yoda');
        $yoda['repos'][] = $repo;
        return $this->save($yoda['repos'], $yaml, $config);
    }
    function remove($repo, $yaml, $config) {
        $repo = str_replace(array('http://','www.'), '', $repo);
        $yoda = $config->c('yoda');
        $yoda['repos'] = array_filter($yoda['repos'], function($r) use($repo) {
            return $r != $repo;
        });
        return $this->save($yoda['repos'], $yaml, $config);
    }
    function save($repos, $yaml, $config) {
        $yoda = $config->c('yoda');
        $yoda['repos'] = $repos;
        if($yaml->save($config->c('yoda.root_dir') . '/yoda.config', $yoda)){
            return true;
        } else {
            throw new \Exception('Hmmmm. Having a problem writing the configuration file I am.');
        }
    }
}
