<?php

declare(strict_types=1);

use Slim\Psr7\Factory\ServerRequestFactory;

$env = 'phpunit';

/** @var Slim\App $web */
$web = require __DIR__ . '/../app/web.php';
$web->run((new ServerRequestFactory())->createFromGlobals());
