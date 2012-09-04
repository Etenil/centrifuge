<?php

/**
 * @file centrifuge
 * This file is part of Centrifuge.
 *
 * @brief This reimplements part of the core into a unit-test
 * framework to test controllers and models.
 *
 * This is meant to be run from the command line.
 *
 * @author Guillaume Pasquet <etenil@etenilsrealm.nl>
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

//ini_set('error_reporting', E_ALL ^E_NOTICE ^E_WARNING ^E_DEPRECATED ^E_STRICT);
ini_set('error_reporting', E_ALL);

// Loading the test library.
require(__DIR__ . '/testcollector.php');
require(__DIR__ . '/test.php');

/*^*********************************
 * Testing library.                *
 ***********************************/
class Runner
{
    protected $collector;
    protected $failed_tests = array();
    protected $success_tests = array();

    function __construct()
    {
        $this->collector = TestCollector::get_instance();
    }

    /**
     * Main runner.
     */
    function run($argc, $argv)
    {
        if($argc > 3) {
            return $this->usage();
        }

        if($argv[1] == '--help') {
            $this->usage();
        }
        else if($argc == 2) {
            $this->run_tests($argv[1]);
        }
        else if($argc == 3) {
            $this->run_tests($argv[1], $argv[2]);
        }
        else {
            $this->run_tests();
        }
    }

    protected function abspath($relpath)
    {
        return $_SERVER['PWD'] . '/' . $relpath;
    }

    protected function test_path($path = '')
    {
        return $this->abspath($path);
    }

    protected function run_testsuite($suite, $test = NULL)
    {
        if($test) {
            printf("Suite %s\n", $suite);
            $testfile = $test . '.php';
            printf("  File %s\n", $test);
            $this->load_tests($this->test_path($suite.'/'), $test . '.php');
        } else {
            $testdir = opendir($this->test_path($suite));
            printf("Suite %s\n", $suite);

            while($test = readdir($testdir)) {
                // Is that a PHP file?
                if(!preg_match('#\.php$#i', $test)
                   || $test[0] == '.'
                   || $test[0] == '#') {
                    continue;
                }

                printf("  File %s\n", $test);
                $this->load_tests($this->test_path($suite.'/'), $test);
            }
            closedir($testdir);
        }
    }

    /**
     * Runs a test suite directory.
     */
    protected function run_tests($testsuite = NULL, $testname = NULL)
    {
        // Let's first run the init stuff.
        if(file_exists($this->abspath('test_init.php')))
            include($this->abspath('test_init.php'));

        if($testsuite && $testname) {
            $this->run_testsuite($testsuite, $testname);
        }
        else if($testsuite) {
            $this->run_testsuite($testsuite);
        }
        else {
            $testdir = opendir($this->test_path());
            while($suite = readdir($testdir)) {
                if(!is_dir($this->test_path($suite))
                   || preg_match('#^\.+$#', $suite)
                   || $suite == 'res') continue;

                $this->run_testsuite($suite);
            }
            closedir($testdir);
        }

        $this->collector->print_summary();
    }

    protected function load_tests($dir, $testname)
    {
        if(!file_exists($dir . $testname)) {
            die(sprintf("Error: file `%s' doesn't exist.\n", $dir . $testname));
        }
        require($dir . $testname);
        $classname = ucfirst(substr($testname, 0, strlen($testname) - 4));

        if(!class_exists($classname)) {
            echo "Warning: class $classname doesn't exist.\n";
            return;
        }

        $refl = new \ReflectionClass($classname);
        $meths = $refl->getMethods();
        $class = new $classname($this->abspath('res/'));

        foreach($meths as $method) {
            $name = $method->name;

            if(!$method->isPublic() || substr($name, 0, 4) != 'test') {
                continue;
            }

            printf("    Running %s ", $name);
            try {
                $class->$name();
            }
            catch(Exception $e) {
                echo "x";
                $this->collector->add_exception($e);
            }

            print "\n";
        }
    }

    /**
     * Help.
     */
    protected function usage()
    {
        printf("Runs unit tests.\n");
        printf("Usage:\n");
        printf("    %s [test suite] [test name]\n", basename(__FILE__));
    }
}

?>

