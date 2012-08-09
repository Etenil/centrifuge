<?php

namespace centrifuge;

class TestCollector
{
    private static $instance;
    protected $failures;
    protected $success;

    protected function __construct()
    {
        $this->failures = array();
        $this->success = array();
    }

    static function get_instance()
    {
        if(!isset(self::$instance)
           || !is_object(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Saves a test failure details.
     */
    function add_failure()
    {
        $trace = debug_backtrace(false);

        for($i = 0; $i < count($trace); $i++) {
            if(!preg_match('/^test/', $trace[$i]['function']))
                continue;

            $this->failures[] = sprintf('%s::%s Failed %s:%s',
                                      $trace[$i]['class'],
                                      $trace[$i]['function'],
                                      $trace[$i - 1]['file'],
                                      $trace[$i - 1]['line']);
        }
    }

    function add_exception(Exception $e)
    {
        global $failed_tests;

        $trace = $e->getTrace();

        for($i = 0; $i < count($trace); $i++) {
            if(!preg_match('/^test/', $trace[$i]['function']))
                continue;

            $this->failures[] = sprintf('%s::%s Failed %s:%s Message: %s',
                                      $trace[$i]['class'],
                                      $trace[$i]['function'],
                                      $trace[$i - 1]['file'],
                                      $trace[$i - 1]['line'],
                                      $e->getMessage());
        }
    }

    /**
     * Logs a success.
     */
    function add_success()
    {
        $trace = debug_backtrace(false);
        foreach($trace as $call) {
            if(!preg_match('/^test/', $call['function']))
                continue;

            $this->success[] = $call['class'].'::'.$call['function'].' Success';
        }
    }

    /**
     * Does the job of printing and keeping records about the tests.
     */
    function printtest($passed)
    {
        if(!$passed) {
            $this->add_failure();
            printf("x");
        } else {
            $this->add_success();
            printf('.');
        }
    }

    /**
     * Prints out a summary of the tests.
     */
    function print_summary()
    {
        $success = count($this->success);
        $failures = count($this->failures);
        $total = $success + $failures;

        print "\n";
        print "Test summary:\n";
        printf("  Total tests:\t\t %d\n", $total);
        printf("  Success:\t\t %d\n", $success);
        printf("  Failures:\t\t %d\n", $failures);
        printf("  Success rate:\t\t %d%%\n", ($success / $total) * 100);

        if($failures) {
            // Printing errors details.
            print "\n";
            print "Failures:\n";
            foreach($this->failures as $test) {
                printf("%s\n", $test);
            }
        }
    }
}