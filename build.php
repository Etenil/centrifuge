<?php

// create with alias "project.phar"
$phar = new Phar('centrifuge.phar', 0, 'centrifuge.phar');
$phar->buildFromDirectory(__DIR__ . '/src');
$phar->setStub($phar->createDefaultStub('centrifuge.php', 'centrifuge.php'));
 
