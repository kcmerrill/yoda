<?php
namespace kcmerrill\yoda;

use SebastianBergmann\Diff\Differ;

class utility {
    function diff($old, $new, $print = true) {
       $differ = new Differ;
       if($print) {
           echo $differ->diff($old, $new);
       } else {
           return $differ->diffToArray($old, $new);
       }
    }
}
