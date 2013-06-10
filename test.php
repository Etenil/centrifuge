<?php

/**
 * @file test.php
 * This file is part of Centrifuge.
 *
 * @brief Unit-testing library for PHP.
 *
 * @author Etenil <boss@etenil.net>
 *
 * @version 1.0
 * @date  9 August 2012
 *
 * Copyright (C)2012 Centrifuge project.
 *
 * Centrifuge is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Centrifuge is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Centrifuge.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace centrifuge;

class Test
{
    protected $collector;
    protected $respath;

    final function __construct($respath)
    {
        $this->respath = $respath;
        $this->collector = TestCollector::get_instance();
        $this->init();
    }

    /**
     * Returns the full path to the test resources folder.
     */
    protected function res($path)
    {
        if(!file_exists($this->respath)) {
            mkdir($this->respath);
        }
        return $this->respath . $path;
    }

    protected function init() {}

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

