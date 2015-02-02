<?php
namespace kcmerrill\yoda;

class shell {
    var $cli;
    function __construct($cli) {
        $this->cli = $cli;
    }

    function executeCommandForeground($command) {
        passthru($command, $results);
    }

    function execute($command, $interactive = false, $ignore_yoda_response = false, $do_not_fail = false , $success = true) {
        $output = $results = false;

        if($interactive){
            passthru($command, $results);
        } else {
            exec($command . ($interactive ?  '' : ' &> /dev/null'), $output, $results);
        }

        //Useful for prompts, etc
        if($ignore_yoda_response) {
            return $success;
        }

        if(!$do_not_fail) {
            $success = $results <= 1 ? $success : false;
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
        if(!$success) {
            exit(1);
        }

        return $success;
    }

    function executeInstructions($instructions, $interactive) {
        foreach($instructions as $command) {
            $this->execute($command, $interactive);
        }
    }
    function executeLiftInstructions($instructions, $config, $interactive = false) {
        $success = true;
        foreach($instructions as $type=>$commands) {
            foreach($commands as $command) {
                $interactive_type = in_array($type, array('prompt','prompt_password','setup','success'));
                $do_not_fail = in_array($type, array('kill','remove'));
                $success = $this->execute($command, $interactive || $interactive_type, $interactive_type, $do_not_fail, $success);
            }
        }
        if($success) {
            foreach($config as $container_name=>$configuration) {
                if(isset($configuration['success'])) {
                    $configuration['success'] = is_string($configuration['success']) ? array($configuration['success']) : $configuration['success'];
                    foreach($configuration['success'] as $command) {
                       $success = $this->execute($command, $interactive, true);
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
