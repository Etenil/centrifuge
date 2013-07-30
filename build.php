<?php

// create with alias "project.phar"
$phar = new Phar('centrifuge.phar', 0, 'centrifuge.phar');
$phar->buildFromDirectory(__DIR__ . '/src');
$stub = $phar->createDefaultStub('centrifuge.php', 'centrifuge.php');
$phar->setStub("#!/usr/bin/env php\n" . $stub);
 
