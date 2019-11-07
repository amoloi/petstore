<?php

declare(strict_types=1);

use Slim\Psr7\Factory\ServerRequestFactory;

/** @var Slim\App $web */
$web = (require __DIR__ . '/../app/web.php')('dev');
$web->run((new ServerRequestFactory())->createFromGlobals());
