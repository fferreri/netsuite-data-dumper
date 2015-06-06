#!/usr/local/bin/php
<?php

require('vendor/autoload.php');

use FFerreri\App;

$app = new App('NetsuiteDumper', '1.0');

$app->addCommands([
    new \FFerreri\Commands\GetRecordsCommand(),
    new \FFerreri\Commands\DumpAllCommand(),
    new \FFerreri\Commands\ExportRecordsCommand(),
]);

$app->run();

