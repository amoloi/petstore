<?php

declare(strict_types=1);

namespace App\ServiceFactory;

use Zend\Diactoros\ResponseFactory;
use Zend\Diactoros\StreamFactory;

final class ApiHttpServiceFactory
{
    /**
     * @return array<string, callable>
     */
    public function __invoke(): array
    {
        return [
            'api-http.response.factory' => static function () {
                return new ResponseFactory();
            },
            'api-http.stream.factory' => static function () {
                return new StreamFactory();
            },
        ];
    }
}
