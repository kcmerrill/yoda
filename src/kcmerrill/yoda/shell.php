<?php
namespace kcmerrill\yoda;

class shell {
    var $cli;
    var $lifted = array();

    function __construct($cli) {
        $this->cli = $cli;
    }

    function executeCommandForeground($command) {
        passthru($command, $results);
    }

    function execute($command, $interactive = false, $ignore_yoda_response = false, $do_not_fail = false ) {
        $output = $results = false;

        if($interactive){
            passthru($command, $results);
        } else {
            exec($command . ($interactive ?  '' : ' &> /dev/null'), $output, $results);
        }

        //Useful for prompts, etc
        if($ignore_yoda_response) {
            return $output;
        }

        //Don't show the user the command, just in case
        $command = str_replace('&> /dev/null', '', $command);
        if($results >= 1 && !$do_not_fail) {
            $this->cli->out('<red>[Do Not]</red> <white>' . $command . '</white>');
            exit(1);
        } else if($results >= 1) {
            $this->cli->out('<yellow>[Worry you should not]</yellow> <white>' . $command . '</white>');
        }else {
            $this->cli->out('<green>[Do]</green> <white>' . $command . '</white>');
        }
        return $output;
    }

    function executeInstructions($instructions, $interactive) {
        foreach($instructions as $command) {
            $this->execute($command, $interactive);
        }
    }
    function executeLiftInstructions($instructions, $config, $interactive = false) {
        foreach($instructions as $type=>$commands) {
            foreach($commands as $command) {
                $interactive_type = in_array($type, array('prompt','prompt_password','setup','success'));
                $do_not_fail = in_array($type, array('kill','remove'));
                $results = $this->execute($command, $interactive || $interactive_type, $interactive_type, $do_not_fail);
            }
        }
        foreach($config as $container_name=>$configuration) {
            if(isset($configuration['success'])) {
                $configuration['success'] = is_string($configuration['success']) ? array($configuration['success']) : $configuration['success'];
                foreach($configuration['success'] as $command) {
                    $success = $this->execute($command, $interactive, true);
                }
            }
            if(isset($configuration['notes'])){
                if(is_string($configuration['notes'])) {
                    $this->cli->green(trim(`echo {$configuration['notes']}`));
                } else {
                    foreach($configuration['notes'] as $note) {
                        $this->cli->green(trim(`echo {$note}`));
                    }
                }
            }
        }
    }
}
