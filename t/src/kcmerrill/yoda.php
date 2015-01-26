<?php

require __DIR__ . '/../../../vendor/autoload.php';

class yodaTest extends PHPUnit_Framework_TestCase
{
    var $yoda;

    public function setUp() {
        $this->yoda = new kcmerrill\yoda(new kcmerrill\yoda\docker, new League\CLImate\CLImate , 'version','');
    }

    public function testYodaIsObj() {
        $this->assertTrue(is_object($this->yoda));
    }

}
