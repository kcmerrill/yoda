<?php
namespace kcmerrill\yoda;

use SebastianBergmann\Diff\Differ;

class utility {
    function diff($old, $new, $print = true) {
       $differ = new Differ;
       $diff = $differ->diff($old, $new);
       if($print) {
        echo $diff;
       } else {
        return $diff;
       }
    }
}
