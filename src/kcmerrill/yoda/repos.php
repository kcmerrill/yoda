<?php
namespace kcmerrill\yoda;

class repos {
    var $config;
    function __construct($config) {
        $this->config = $config;
    }

    function get() {
        $repos = $this->config->get('yoda.repos', array());
        $repos[] = 'yoda.kcmerrill.com';
        array_unshift($repos, 'yoda.' . gethostname());
        return array_unique($repos);
    }
}
