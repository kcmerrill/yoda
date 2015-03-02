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
}
