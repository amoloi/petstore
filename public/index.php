<?php

declare(strict_types=1);

use Peak\Http\Response\Emitter;
use Slim\Psr7\Factory\ServerRequestFactory;

/** @var Peak\Http\Stack $stack */
$stack = (require __DIR__ . '/../app/web.php')(getenv('APP_ENV'));
(new Emitter())->emit($stack->handle((new ServerRequestFactory())->createFromGlobals()));
