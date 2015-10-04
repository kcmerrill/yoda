<?php
namespace kcmerrill\yoda;

class repos {
    var $config;

    function __construct($config) {
        $this->config = $config;
    }

    function get($reverse = false, $running = false) {
        $repos = $this->config->get('yoda.repos', array());
        array_unshift($repos, 'yoda.kcmerrill.com');
        array_unshift($repos, 'yoda.' . gethostname());
        $repos = array_unique($repos);
        $repos = $reverse ?  array_reverse($repos) : $repos;
        if($running) {
            $validated = array_fill_keys($repos, false);
            foreach($validated as $url=>$up) {
                $validated[$url] = $this->valid($url);
            }
            return $validated;
        } else {
            return $repos;
        }
    }

    function valid($repo) {
        return file_get_contents('http://' . $repo . '/shares/') ? true : false;
    }

    function add($repo) {
        $repo = str_replace(array('http://','www.'), '', $repo);

        /* Do a simple test to see if it's a valid yoda repo */
        if(!$this->valid($repo)) {
            throw new \Exception('Invalid, the repository ' . $repo . ' is.');
        }

        $repos = $this->config->c('yoda.repos');
        $repos[] = $repo;
        $this->config->c('yoda.repos', $repos);
        return $this->save();
    }

    function remove($repo) {
        $repo = str_replace(array('http://','www.'), '', $repo);
        $yoda = $this->config->c('yoda');
        $repos = array_filter($yoda['repos'], function($r) use($repo) {
            return $r != $repo;
        });

        $this->config->c('yoda.repos', $repos);
        return $this->save();
    }

    function save() {
        return $this->config->save('yoda', $this->config->c('yoda.system.config_dir') . DIRECTORY_SEPARATOR . $this->config->c('yoda.system.config_name'));
    }
}
