<?php

declare(strict_types=1);

/** @var Zend\Expressive\Application $web */

$env = 'dev';

$web = require __DIR__ . '/../app/web.php';
$web->run();
