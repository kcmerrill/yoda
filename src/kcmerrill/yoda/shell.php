<?php

namespace kcmerrill\yoda;

class shell {

    var $cli;
    function __construct($cli) {
        $this->cli = $cli;
    }
    function executeInstructions($instructions, $config) {
        $success = true;
        foreach($instructions as $type=>$commands) {
            foreach($commands as $command) {
                $output = $results = false;
                exec($command . ($type == 'prompt' ?  '' : '>/dev/null 2>/dev/null'), $output, $results);
                if($type == 'prompt') {
                    echo implode("\n", $output) . PHP_EOL;
                    continue;
                }
                if(!in_array($type, array('remove','kill'))) {
                    $success = $results === 0 ? $success : false;
                }
                if($results >= 1 && !$success) {
                    $this->cli->out('<red>[Do Not]</red> <white>' . $command . '</white>');
                } else if($results >= 1) {
                    $this->cli->out('<yellow>[Worry you should not]</yellow> <white>' . $command . '</white>');
                }else {
                    $this->cli->out('<green>[Do]</green> <white>' . $command . '</white>');
                }
                if(!$success) {
                    exit(1);
                }
            }
        }
        if($success) {
            foreach($config as $container_name=>$configuration) {
                if(isset($configuration['notes'])){
                    if(is_string($configuration['notes'])) {
                        $this->cli->green($configuration['notes']);
                    } else {
                        foreach($configuration['notes'] as $note) {
                            $this->cli->green($note);
                        }
                    }
                }
            }
        }
    }
}
