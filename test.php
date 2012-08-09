<?php

/**
 * @file test.php
 * This file is part of Centrifuge.
 *
 * @brief Unit-testing library for Movicon.
 *
 * @author Etenil <boss@etenil.net>
 *
 * @version 1.0
 * @date  7 March 2011
 *
 * Copyright (C)2011 Movicon project.
 *
 * See COPYING for licensing information.
 */

namespace centrifuge;

class Test
{
    protected $collector;

    function __construct()
    {
        $this->collector = TestCollector::get_instance();
    }

    /**
     * Signals an error if the contained code returns false.
     */
    protected function assert($stuff)
    {
        $this->collector->printtest($stuff);
    }

    /**
     * Ensures the provided expression returns false.
     */
    protected function nassert($stuff)
    {
        $this->collector->printtest(!$stuff);
    }

    /**
     * Checks if $stuff is equal to $expectedstuff.
     */
    protected function equals($stuff, $expectedstuff)
    {
        if(is_array($stuff) && is_array($expectedstuff)) {
            $this->collector->printtest($this->compare_aarray($stuff, $expectedstuff));
        } else {
            $this->collector->printtest($stuff == $expectedstuff);
        }
    }

    /**
     * Ensures that the two given item are not equal.
     */
    protected function differs($stuff, $expectedstuff)
    {
        if(is_array($stuff) && is_array($expectedstuff)) {
            $this->collector->printtest(!$this->compare_aarray($stuff, $expectedstuff));
        } else {
            $this->collector->printtest($stuff != $expectedstuff);
        }
    }

    /**
     * Compares two associative arrays.
     */
    protected function compare_aarray($array1, $array2)
    {
        $same = true;
        if(!is_array($array1) || !is_array($array2)) {
            $same = false;
        }
        if(count($array1) != count($array2)) {
            $same = false;
        }
        foreach($array1 as $key => $val) {
            if(!isset($array2[$key]) || $array2[$key] != $val) {
                $same = false;
            }
        }
        return $same;
    }

    /**
     * Checks if value matches provided preg $regex
     */
    protected function match($regex, $value)
    {
        $this->collector->printtest(preg_match($regex, $value));
    }

    /**
     * ensures that $value matches $pattern, whitespaces being
     * specified as '?'.
     */
    protected function whitespace($pattern, $value)
    {
        $replacements = array(
            '(' => '\\(',
            ')' => '\\)',
            '[' => '\\[',
            ']' => '\\]',
            '{' => '\\{',
            '}' => '\\}',
            '*' => '\\*',
            '+' => '\\+',
            '?' => '\\s*',
            );

        foreach($replacements as $f => $r) {
            $pattern = str_replace($f, $r, $pattern);
        }

        $regex = '#^' . $pattern . '$#';
        $this->collector->printtest(preg_match($regex, $value));
    }
}

?>
