<?php

declare(strict_types=1);

/** @var Zend\Expressive\Application $app */

$env = 'dev';

$app = require __DIR__ . '/../app/app.php';
$app->run();
