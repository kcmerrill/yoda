<?php
namespace kcmerrill\yoda;

class updater {
    var $root_dir;
    var $updater_file;

    function __construct($config) {
        $this->root_dir = $config->c('yoda.system.root_dir');
        $this->updater_file = $this->root_dir . '/yoda.last_updated';
    }

    function check(){
        if(is_file($this->updater_file) && filemtime($this->updater_file) + 604800 <= time()) {
            return true;
        } else {
            if(!is_file($this->updater_file)) {
                touch($this->updater_file);
            }
        }
        return false;
    }
}
