<?php

declare(strict_types=1);

/** @var Zend\Expressive\Application $web */
$web = (require __DIR__ . '/../app/web.php')(getenv('APP_ENV'));
$web->run();
