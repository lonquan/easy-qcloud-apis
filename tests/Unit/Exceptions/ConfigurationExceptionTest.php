<?php

declare(strict_types=1);

namespace EasyQCloudApi\Tests\Unit\Exceptions;

use EasyQCloudApi\Exceptions\ConfigurationException;
use EasyQCloudApi\Exceptions\QCloudException;
use PHPUnit\Framework\TestCase;

/**
 * ConfigurationException 测试类
 */
class ConfigurationExceptionTest extends TestCase
{
    public function test_missing_configuration(): void
    {
        $exception = ConfigurationException::missingConfiguration('test_key');

        $this->assertInstanceOf(ConfigurationException::class, $exception);
        $this->assertInstanceOf(QCloudException::class, $exception);
        $this->assertEquals('Missing required configuration: test_key', $exception->getMessage());
    }

    public function test_invalid_configuration(): void
    {
        $exception = ConfigurationException::invalidConfiguration('test_key', 'Invalid value provided');

        $this->assertInstanceOf(ConfigurationException::class, $exception);
        $this->assertInstanceOf(QCloudException::class, $exception);
        $this->assertEquals("Invalid configuration for 'test_key': Invalid value provided", $exception->getMessage());
    }
}
