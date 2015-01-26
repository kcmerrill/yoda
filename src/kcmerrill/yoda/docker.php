<?php

namespace kcmerrill\yoda;

class docker {
    function stop($container_name) {
        return "docker stop {$container_name}";
    }
    function start($container_name) {
        return "docker start {$container_name}";
    }
    function kill($container_name) {
        return "docker kill {$container_name}";
    }
    function pull($image) {
        return "docker pull {$image}";
    }
    function build($image, $dockerfile) {
        return "docker build -t {$image} {$dockerfile}";
    }
    function remove($container_name) {
        return "docker rm -f {$container_name}";
    }
    function run($image, $options = array()){
        $options = is_array($options) ? $options : array();
        $run_cmd = array('docker run');
        foreach($options as $c=>$value) {
            $value = is_array($value) ? $value : array($value);
            foreach($value as $v){
                if(strlen($c) == 1) {
                    $run_cmd[] = is_bool($v) ? "-{$c}" : "-{$c} {$v}";
                } else {
                    $run_cmd[] = is_bool($v) ? "--{$c}" : "--{$c}={$v}";
                }
            }
        }
        $run_cmd[] = $image;
        return implode(' ', $run_cmd);
    }
}
