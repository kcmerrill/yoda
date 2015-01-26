<?php

require __DIR__ . '/../../../../vendor/autoload.php';

class dockerTest extends PHPUnit_Framework_TestCase
{
    var $docker;

    public function setUp() {
        $this->docker = new kcmerrill\yoda\docker;
    }

    public function testDockerIsObj() {
        $this->assertTrue(is_object($this->docker));
    }

    public function testDockerStop(){
        $results = $this->docker->stop('kcmerrill');
        $this->assertEquals('docker stop kcmerrill',$results);
    }

    public function testDockerStart(){
        $results = $this->docker->run('kcmerrill');
        $this->assertEquals('docker run kcmerrill', $results);
    }

    public function testDockerKill(){
        $results = $this->docker->kill('kcmerrill');
        $this->assertEquals('docker kill kcmerrill', $results);
    }

    public function testDockerPull(){
        $results = $this->docker->pull('someimage/kcmerrill');
        $this->assertEquals('docker pull someimage/kcmerrill', $results);
    }

    public function testDockerRun(){
        $options = array(
            'rm'=>true,
            'p'=>'80:80',
            'v'=>array('a:b','c:d')
        );
        $results = $this->docker->run('vitaminc', $options);
        $this->assertEquals('docker run --rm -p 80:80 -v a:b -v c:d vitaminc', $results);
    }
}
