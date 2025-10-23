<?php

declare(strict_types=1);

namespace EasyQCloudApi\Tests\Unit\Support;

use EasyQCloudApi\Exceptions\ConfigurationException;
use EasyQCloudApi\Support\Config;
use PHPUnit\Framework\TestCase;

/**
 * Config 测试类
 */
class ConfigTest extends TestCase
{
    private Config $config;

    protected function setUp(): void
    {
        $this->config = new Config([
            'profiles' => [
                'default' => [
                    'secret_id' => 'test_secret_id',
                    'secret_key' => 'test_secret_key',
                ],
            ],
            'services' => [
                'ocr' => [
                    'region' => 'ap-beijing',
                    'version' => '2018-05-22',
                ],
            ],
        ]);
    }

    public function test_get_config_value(): void
    {
        $this->assertEquals('test_secret_id', $this->config->get('profiles.default.secret_id'));
        $this->assertEquals('ap-beijing', $this->config->get('services.ocr.region'));
        $this->assertNull($this->config->get('non.existent.key'));
        $this->assertEquals('default_value', $this->config->get('non.existent.key', 'default_value'));
    }

    public function test_set_config_value(): void
    {
        $this->config->set('new.key', 'new_value');
        $this->assertEquals('new_value', $this->config->get('new.key'));
    }

    public function test_has_config_key(): void
    {
        $this->assertTrue($this->config->has('profiles.default.secret_id'));
        $this->assertFalse($this->config->has('non.existent.key'));
    }

    public function test_validate_valid_config(): void
    {
        $this->expectNotToPerformAssertions();
        $this->config->validate();
    }

    public function test_validate_missing_profiles(): void
    {
        $config = new Config(['services' => []]);

        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Missing required configuration: profiles');
        $config->validate();
    }

    public function test_validate_missing_secret_id(): void
    {
        $config = new Config([
            'profiles' => [
                'default' => [
                    'secret_key' => 'test_secret_key',
                ],
            ],
            'services' => [
                'ocr' => [
                    'region' => 'ap-beijing',
                ],
            ],
        ]);

        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('secret_id is required and cannot be empty');
        $config->validate();
    }

    public function test_validate_missing_secret_key(): void
    {
        $config = new Config([
            'profiles' => [
                'default' => [
                    'secret_id' => 'test_secret_id',
                ],
            ],
            'services' => [
                'ocr' => [
                    'region' => 'ap-beijing',
                ],
            ],
        ]);

        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('secret_key is required and cannot be empty');
        $config->validate();
    }

    public function test_validate_missing_region(): void
    {
        $config = new Config([
            'profiles' => [
                'default' => [
                    'secret_id' => 'test_secret_id',
                    'secret_key' => 'test_secret_key',
                ],
            ],
            'services' => [
                'ocr' => [],
            ],
        ]);

        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('region is required and cannot be empty');
        $config->validate();
    }
}
