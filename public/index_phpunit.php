<?php

declare(strict_types=1);

/** @var Zend\Expressive\Application $web */

$env = 'phpunit';

$web = require __DIR__ . '/../app/web.php';
$web->run();
