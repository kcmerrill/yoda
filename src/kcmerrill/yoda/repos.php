<?php
namespace kcmerrill\yoda;

class repos {
    var $config;
    function __construct($config) {
        $this->config = $config;
    }

    function get($reverse = false) {
        $repos = $this->config->get('yoda.repos', array());
        $repos[] = 'yoda.kcmerrill.com';
        array_unshift($repos, 'yoda.' . gethostname());
        return $reverse ?  array_reverse($repos) : array_unique($repos);
    }
}
