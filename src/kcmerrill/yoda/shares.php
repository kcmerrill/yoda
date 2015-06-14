<?php
namespace kcmerrill\yoda;

class shares {
    function __construct($config) {
        $this->root_dir = $config->c('yoda.system.root_dir');
    }

    function get($repos, $to_find = false){
        $shares = array();
        foreach($repos as $repo) {
            try{
                $repo_share = file_get_contents('http://' . $repo . '/shares/' . ($to_find ? $to_find : ''));
                $repo_share = json_decode($repo_share, TRUE);
                if(is_array($repo_share)) {
                    $shares = array_merge($shares, $repo_share);
                }
            }
            catch(\Exception $e) {
                continue;
            }
        }
        return $shares;
    }
}
