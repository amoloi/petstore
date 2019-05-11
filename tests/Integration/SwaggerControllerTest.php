<?php

declare(strict_types=1);

namespace App\Tests\Integration;

final class SwaggerControllerTest extends AbstractIntegrationTest
{
    public function testIndex(): void
    {
        $response = $this->httpRequest(
            'GET',
            '/api'
        );

        self::assertSame(200, $response['status']['code']);

        self::assertStringStartsWith('text/html', $response['headers']['content-type'][0]);
        self::assertStringContainsString('<title>Swagger UI</title>', $response['body']);
    }

    public function testYaml(): void
    {
        $response = $this->httpRequest(
            'GET',
            '/api/swagger.yml'
        );

        self::assertSame(200, $response['status']['code']);

        self::assertStringStartsWith('application/x-yaml', $response['headers']['content-type'][0]);

        self::assertStringContainsString('title: Swagger Petstore', $response['body']);
    }
}