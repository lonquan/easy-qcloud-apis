<?php

declare(strict_types=1);

namespace EasyQCloudApi\Tests\Unit\Exceptions;

use EasyQCloudApi\Exceptions\QCloudException;
use EasyQCloudApi\Exceptions\RequestException;
use PHPUnit\Framework\TestCase;

/**
 * RequestException 测试类
 */
class RequestExceptionTest extends TestCase
{
    public function test_constructor(): void
    {
        $exception = new RequestException('Test message', 400, ['error' => 'test']);

        $this->assertInstanceOf(RequestException::class, $exception);
        $this->assertInstanceOf(QCloudException::class, $exception);
        $this->assertEquals('Test message', $exception->getMessage());
        $this->assertEquals(400, $exception->getStatusCode());
        $this->assertEquals(['error' => 'test'], $exception->getResponse());
    }

    public function test_http_error(): void
    {
        $exception = RequestException::httpError(404, 'Not Found', ['error' => 'not_found']);

        $this->assertInstanceOf(RequestException::class, $exception);
        $this->assertEquals('HTTP Error 404: Not Found', $exception->getMessage());
        $this->assertEquals(404, $exception->getStatusCode());
        $this->assertEquals(['error' => 'not_found'], $exception->getResponse());
    }

    public function test_network_error(): void
    {
        $previous = new \Exception('Network timeout');
        $exception = RequestException::networkError('Connection failed', $previous);

        $this->assertInstanceOf(RequestException::class, $exception);
        $this->assertEquals('Network Error: Connection failed', $exception->getMessage());
        $this->assertEquals(0, $exception->getStatusCode());
        $this->assertEquals([], $exception->getResponse());
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function test_get_status_code(): void
    {
        $exception = new RequestException('Test', 500);
        $this->assertEquals(500, $exception->getStatusCode());
    }

    public function test_get_response(): void
    {
        $response = ['error' => 'test'];
        $exception = new RequestException('Test', 400, $response);
        $this->assertEquals($response, $exception->getResponse());
    }
}
