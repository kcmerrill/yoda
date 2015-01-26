<?php
namespace kcmerrill\yoda;

class shell {
    var $cli;
    function __construct($cli) {
        $this->cli = $cli;
    }

    function executeCommandForeground($cmd) {
        passthru($command, $results);
    }

    function execute($command, $interactive, $cli_output = false, $success = true) {
        $output = $results = false;
        if($interactive){
            passthru($command, $results);
        } else {
            exec($command . ($cli_output ?  '' : '>/dev/null 2>/dev/null'), $output, $results);
        }
        if($cli_output) {
            echo implode("\n", $output) . PHP_EOL;
            return $success;
        }
        if(!in_array($type, array('remove','kill'))) {
            $success = $results === 0 ? $success : false;
        }
        if($results >= 2 && !$success) {
            $this->cli->out('<red>[Do Not]</red> <white>' . $command . '</white>');
        } else if($results >= 1) {
            $this->cli->out('<yellow>[Worry you should not]</yellow> <white>' . $command . '</white>');
        }else {
            $this->cli->out('<green>[Do]</green> <white>' . $command . '</white>');
        }
        if(!$success) {
            exit(1);
        }

        return $success;
    }

    function executeInstructions($instructions, $config, $interactive = false) {
        $success = true;
        foreach($instructions as $type=>$commands) {
            foreach($commands as $command) {
                $success = $this->execute($command, $interactive, in_array($type, array('prompt','prompt_password','setup','success')), $success);
            }
        }
        if($success) {
            foreach($config as $container_name=>$configuration) {
                if(isset($configuration['success'])) {
                    $configuration['success'] = is_string($configuration['success']) ? array($configuration['success']) : $configuration['success'];
                    foreach($configuration['success'] as $command) {
                       $success = $this->execute($command, $interactive);
                    }
                }
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
