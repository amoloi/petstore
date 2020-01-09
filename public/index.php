<?php

declare(strict_types=1);

use Peak\Http\Response\Emitter;
use Slim\Psr7\Factory\ServerRequestFactory;

/** @var Peak\Bedrock\Http\Application $app */
$app = (require __DIR__ . '/../app/web.php')(getenv('APP_ENV'));
$app->run((new ServerRequestFactory())->createFromGlobals(), new Emitter());
