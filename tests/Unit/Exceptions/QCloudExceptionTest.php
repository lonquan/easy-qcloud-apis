<?php

declare(strict_types=1);

namespace EasyQCloudApi\Tests\Unit\Exceptions;

use EasyQCloudApi\Exceptions\QCloudException;
use PHPUnit\Framework\TestCase;

/**
 * QCloudException 测试类
 */
class QCloudExceptionTest extends TestCase
{
    public function test_configuration_error(): void
    {
        $exception = QCloudException::configurationError('Test configuration error');

        $this->assertInstanceOf(QCloudException::class, $exception);
        $this->assertEquals('Configuration Error: Test configuration error', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
    }

    public function test_request_error(): void
    {
        $exception = QCloudException::requestError('Test request error');

        $this->assertInstanceOf(QCloudException::class, $exception);
        $this->assertEquals('Request Error: Test request error', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
    }

    public function test_signature_error(): void
    {
        $exception = QCloudException::signatureError('Test signature error');

        $this->assertInstanceOf(QCloudException::class, $exception);
        $this->assertEquals('Signature Error: Test signature error', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
    }

    public function test_validation_error(): void
    {
        $exception = QCloudException::validationError('Test validation error');

        $this->assertInstanceOf(QCloudException::class, $exception);
        $this->assertEquals('Validation Error: Test validation error', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
    }

    public function test_exception_with_previous(): void
    {
        $previous = new \Exception('Previous exception');
        $exception = QCloudException::configurationError('Test error', $previous);

        $this->assertSame($previous, $exception->getPrevious());
    }
}
