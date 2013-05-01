<?php

require '../classes/model.php';


class ModelTest extends PHPUnit_Framework_TestCase
{
    private $model;

    public function setUp()
    {
        $this->model = new Polyglott_Model('en', 'en', './data/');
    }

    public function testOtherLanguages()
    {
        $expected = array('de');
        $actual = $this->model->otherLanguages();
        $this->assertEquals($expected, $actual);
    }
}


?>