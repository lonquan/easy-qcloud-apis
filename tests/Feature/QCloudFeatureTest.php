<?php

declare(strict_types=1);

namespace EasyQCloudApi\Tests\Feature;

use EasyQCloudApi\Clients\CaptchaClient;
use EasyQCloudApi\Clients\FaceIdClient;
use EasyQCloudApi\Clients\OcrClient;
use EasyQCloudApi\Exceptions\ConfigurationException;
use EasyQCloudApi\QCloudFactory;
use EasyQCloudApi\Support\Config;
use PHPUnit\Framework\TestCase;

/**
 * QCloud 功能测试
 */
class QCloudFeatureTest extends TestCase
{
    public function test_complete_ocr_workflow(): void
    {
        $config = new Config([
            'profiles' => [
                'default' => [
                    'secret_id' => 'test_secret_id',
                    'secret_key' => 'test_secret_key',
                ],
            ],
            'services' => [
                'ocr' => [
                    'region' => 'ap-beijing',
                    'version' => '2018-11-19',
                ],
            ],
        ]);

        $factory = new QCloudFactory($config);
        $ocrClient = $factory->make('ocr');

        $this->assertInstanceOf(OcrClient::class, $ocrClient);
        $this->assertEquals('ocr', $ocrClient->getServiceName());
    }

    public function test_complete_faceid_workflow(): void
    {
        $config = new Config([
            'profiles' => [
                'default' => [
                    'secret_id' => 'test_secret_id',
                    'secret_key' => 'test_secret_key',
                ],
            ],
            'services' => [
                'faceid' => [
                    'region' => 'ap-beijing',
                    'version' => '2018-03-01',
                ],
            ],
        ]);

        $factory = new QCloudFactory($config);
        $faceIdClient = $factory->make('faceid');

        $this->assertInstanceOf(FaceIdClient::class, $faceIdClient);
        $this->assertEquals('faceid', $faceIdClient->getServiceName());
    }

    public function test_complete_captcha_workflow(): void
    {
        $config = new Config([
            'profiles' => [
                'default' => [
                    'secret_id' => 'test_secret_id',
                    'secret_key' => 'test_secret_key',
                ],
            ],
            'services' => [
                'captcha' => [
                    'region' => 'ap-shanghai',
                    'version' => '2019-07-22',
                ],
            ],
        ]);

        $factory = new QCloudFactory($config);
        $captchaClient = $factory->make('captcha');

        $this->assertInstanceOf(CaptchaClient::class, $captchaClient);
        $this->assertEquals('captcha', $captchaClient->getServiceName());
    }

    public function test_multi_profile_configuration(): void
    {
        $config = new Config([
            'profiles' => [
                'production' => [
                    'secret_id' => 'prod_secret_id',
                    'secret_key' => 'prod_secret_key',
                ],
                'testing' => [
                    'secret_id' => 'test_secret_id',
                    'secret_key' => 'test_secret_key',
                ],
            ],
            'services' => [
                'ocr' => [
                    'region' => 'ap-beijing',
                    'version' => '2018-11-19',
                ],
            ],
        ]);

        $factory = new QCloudFactory($config);

        // 测试生产环境配置
        $prodClient = $factory->make('ocr', 'production');
        $this->assertEquals('production', $prodClient->getDefaultAccessKey());

        // 测试测试环境配置
        $testClient = $factory->make('ocr', 'testing');
        $this->assertEquals('testing', $testClient->getDefaultAccessKey());
    }

    public function test_multi_service_configuration(): void
    {
        $config = new Config([
            'profiles' => [
                'default' => [
                    'secret_id' => 'test_secret_id',
                    'secret_key' => 'test_secret_key',
                ],
            ],
            'services' => [
                'ocr' => [
                    'region' => 'ap-beijing',
                    'version' => '2018-11-19',
                ],
                'faceid' => [
                    'region' => 'ap-shanghai',
                    'version' => '2018-03-01',
                ],
                'captcha' => [
                    'region' => 'ap-guangzhou',
                    'version' => '2019-07-22',
                ],
            ],
        ]);

        $factory = new QCloudFactory($config);

        // 测试不同服务的配置
        $ocrClient = $factory->make('ocr');
        $faceIdClient = $factory->make('faceid');
        $captchaClient = $factory->make('captcha');

        $this->assertInstanceOf(OcrClient::class, $ocrClient);
        $this->assertInstanceOf(FaceIdClient::class, $faceIdClient);
        $this->assertInstanceOf(CaptchaClient::class, $captchaClient);
    }

    public function test_configuration_validation(): void
    {
        // 测试缺失profiles配置
        $config = new Config([
            'services' => [
                'ocr' => [
                    'region' => 'ap-beijing',
                    'version' => '2018-11-19',
                ],
            ],
        ]);

        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('Missing required configuration: profiles');

        $config->validate();
    }

    public function test_configuration_validation_missing_secret_id(): void
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
                    'version' => '2018-11-19',
                ],
            ],
        ]);

        $this->expectException(ConfigurationException::class);
        $this->expectExceptionMessage('secret_id is required and cannot be empty');

        $config->validate();
    }

    public function test_configuration_validation_missing_region(): void
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

    public function test_factory_with_invalid_service(): void
    {
        $config = new Config([
            'profiles' => [
                'default' => [
                    'secret_id' => 'test_secret_id',
                    'secret_key' => 'test_secret_key',
                ],
            ],
            'services' => [
                'ocr' => [
                    'region' => 'ap-beijing',
                    'version' => '2018-11-19',
                ],
            ],
        ]);

        $factory = new QCloudFactory($config);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Missing required configuration: services.invalid_service');

        $factory->make('invalid_service');
    }
}
