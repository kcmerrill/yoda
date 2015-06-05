<?php
namespace kcmerrill\yoda;

class instruct {
    var $docker;
    var $instructions;
    var $args;

    function __construct($docker, $args) {
        $this->args = $args;
        $this->docker = $docker;
        $this->instructions = array(
            'prompt'=>array(),
            'setup'=>array(),
            'pull'=>array(),
            'build'=>array(),
            'kill'=>array(),
            'remove'=>array(),
            'start'=>array(),
            'success'=>array(),
            'run'=>array(),
            'require'=>array(),
            'update'=>array()
        );
    }

    function controlParse($command) {
        if(is_array($this->args)) {
            foreach($this->args as $index=>$value) {
                $command = str_replace(':' . $index, $value, $command);
            }
            return $command;
        } else {
            return $command;
        }
    }

    function control($containers_configuration, $specific_env = false) {
        $control = array();
        if($specific_env && isset($containers_configuration['env'][$specific_env])) {
           $config = $containers_configuration[$specific_env];
           $config['control'] = is_array($config['control']) ? $config['control'] : array('bash');
           foreach($config['control'] as $command) {
                $control[] = $this->docker->exec($config['name'], $this->controlParse($command));
           }
        } else {
            $default_behavior = true;
            if($default_behavior) {
                $config = end($containers_configuration);
                $config['control'] = is_array($config['control']) ? $config['control'] : array('bash');
                foreach($config['control'] as $command) {
                    $control[] = $this->docker->exec($config['name'], $this->controlParse($command));
                }
            }
        }
        return $control;
    }
    function lift($containers_configuration) {
       $setup = is_file('.yoda.setup');
       foreach($containers_configuration as $container=>$config) {
            if(!$setup && $config['prompt']) {
                foreach($config['prompt'] as $read=>$question) {
                    $this->instructions['prompt'][] = 'echo "' . $question . '"';
                    $this->instructions['prompt'][] = 'read ' . $read;
                }
            }
            if(!$setup && $config['prompt_password']) {
                foreach($config['prompt_password'] as $read=>$question) {
                    $this->instructions['prompt'][] = 'echo "' . $question . '"';
                    $this->instructions['prompt'][] = 'stty -echo';
                    $this->instructions['prompt'][] = 'read ' . $read;
                    $this->instructions['prompt'][] = 'stty echo';
                }
            }
            if(!$setup && $config['setup']) {
                if(is_string($config['setup'])) {
                    $config['setup'] = array($config['setup']);
                }
                foreach($config['setup'] as $command) {
                    $this->instructions['setup'][] = $command;
                }
            }
            if($config['pull']) {
                if(is_bool($config['pull'])) {
                    $this->instructions['pull'][] = $this->docker->pull($config['image']);
                } else {
                    $config['pull'] = is_array($config['pull']) ? $config['pull'] : array($config['pull']);
                    foreach($config['pull'] as $pull) {
                        $this->instructions['pull'][] = $this->docker->pull($pull);
                    }
                }

            }
            if($config['build'] && is_string($config['build'])) {
                $this->instructions['build'][] = $this->docker->build($config['image'],  $config['build']);
            }

            // Stop the container
            $this->instructions['kill'][] = $this->docker->kill($config['name']);

            if($config['remove']) {
                $this->instructions['remove'][] = $this->docker->remove($config['name']);
                $this->instructions['run'][] = $this->docker->run($config['image'], $config['run']);
            } else {
                $this->instructions['start'][] = $this->docker->start($config['name']) . " || " . $this->docker->run($config['image'], $config['run']);
            }
        }
        return $this->instructions;
    }
}
