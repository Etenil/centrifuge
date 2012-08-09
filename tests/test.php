<?php

class Test extends Centrifuge\Test
{
    function testAssert()
    {
        $this->assert(true);
    }

    function testNassert()
    {
        $this->nassert(false);
    }

    function testCompare_aarray()
    {
        $arr1 = array('a' => 2,
                      'b' => 6);
        $arr2 = array('a' => 2,
                      'b' => 8);
        $this->assert($this->compare_aarray($arr1, $arr1));
        $this->nassert($this->compare_aarray($arr1, $arr2));

        $aarr = array(
            'a' => 2,
            'b' => array(
                't' => 'thing',
                'b' => 'another thing'
                ),
            );
        $this->assert($this->compare_aarray($aarr, $aarr));
        $this->nassert($this->compare_aarray($aarr, $arr1));
    }

    function testEquals()
    {
        $this->equals(1, 1);
        $this->equals(array(1, 2), array(1, 2));
    }

    function testDiffers()
    {
        $this->differs(1, 2);
        $this->differs(array(1, 2), array(2, 1));
        $this->differs(array(1, 2), array(2, 3));
    }

    function testMatch()
    {
        $this->match('/^abc/', 'abcdef');
        $this->match('/def$/', 'abcdef');
    }

    function testWhitespace()
    {
        $this->whitespace('stuff?', 'stuff        ');
        $this->whitespace('stu?ff', 'stu     ff');
    }

    function testIMustFail()
    {
        $this->assert(false);
    }
}
