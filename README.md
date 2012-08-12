CENTRIFUGE
==========

DESCRIPTION
-----------
Centrifuge is a unit tests framework for PHP. Its goal is to provide
only the most useful testing functions and to remain very
simple. Centrifuge already supports test suite, resource
initialisation. This document will describe how to use these features.


USING CENTRIFUGE
----------------
Centrifuge is a command-line tool. It assumes that the command is
invoked within the tests root directory. Invoking Centrifuge is done
as follows (assuming it is in your PATH):

    $ centrifuge [suite] [test]

You can optionally specify which test suite to run and which specific
test within the specified test suite to run.


ORGANISING TESTS
----------------
The runner assumes that you organised your tests in a logical
way. Tests are single php scripts and test suites are folders
containing those. The typical test setup would be as follows:

    -- Suite1
    |  |- test1.php
    |  `- test2.php
    `- Suite2
       |- test1.php
       `- test2.php


WRITING TESTS
-------------
Unit tests are organised in classes. The class must have the same name
as the file that contains it (this is case-insensitive), and methods
that need to be run must start with the prefix `test'.

Unit test classes must extend the basic class centrifuge\Test.

Below is an example of our imaginary unit test Test1, which resides in
the file test1.php, within the suite Suite1.

    class Test1 extends centrifuge\Test
    {
        protected $value;

        function init()
        {
            $this->value = rand(0, 10);
        }

        function testEquals()
        {
            $this->equals($this->value, $this->value);
        }
    }

In this example, only testEquals() will be called by the runner, other
methods are ignored.

The constructor of the base class isn't available. The correct way to
add initialisation code is to overload the method init(), like done
above. The destructor remains available if you need to do some tidy up
after the tests.


TEST METHODS
------------
The basic class centrifuge\Test provides a number of assertion
methods. Below is a list of those.

assert($val)
:   ensures that $val evaluates to TRUE;
nassert($val)
:   ensures that $val does not evaluate to TRUE;
equals($val1, $val2)
:   ensures that $val1 and $val2 are equivalent. This test does not
    compare the types, i.e. it uses == and not ===. Arrays are
    supported and are tested recursively;
differs($val1, $val2)
:   asserts that $val1 and $val2 are different. As for equals, this
    also supports arrays;
match($regex, $val)
:   asserts that $val matches the provided preg $regex. Don't forget
    the delimiters in the regex;
whitespace($pattern, $val)
:   asserts that $val matches a whitespaced pattern where whitespace
    is represented as '?'. For example 'test?1' will match 'test 1'
    and 'test      1';
