<?php

declare(strict_types=1);

use Slim\Psr7\Factory\ServerRequestFactory;

/** @var Slim\App $web */
$web = (require __DIR__ . '/../app/web.php')('phpunit');
$web->run((new ServerRequestFactory())->createFromGlobals());
