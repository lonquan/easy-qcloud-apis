<?php

declare(strict_types=1);

namespace EasyQCloudApi\Tests\Integration;

use EasyQCloudApi\Clients\CaptchaClient;
use EasyQCloudApi\Clients\FaceIdClient;
use EasyQCloudApi\Clients\OcrClient;
use EasyQCloudApi\QCloudFactory;
use EasyQCloudApi\Support\Config;
use EasyQCloudApi\Support\Logger;
use PHPUnit\Framework\TestCase;

/**
 * QCloudFactory 集成测试
 */
class QCloudFactoryIntegrationTest extends TestCase
{
    private QCloudFactory $factory;

    protected function setUp(): void
    {
        $config = new Config([
            'profiles' => [
                'default' => [
                    'secret_id' => 'test_secret_id',
                    'secret_key' => 'test_secret_key',
                ],
                'production' => [
                    'secret_id' => 'prod_secret_id',
                    'secret_key' => 'prod_secret_key',
                ],
            ],
            'services' => [
                'ocr' => [
                    'region' => 'ap-beijing',
                    'version' => '2018-11-19',
                ],
                'faceid' => [
                    'region' => 'ap-beijing',
                    'version' => '2018-03-01',
                ],
                'captcha' => [
                    'region' => 'ap-shanghai',
                    'version' => '2019-07-22',
                ],
            ],
        ]);

        $this->factory = new QCloudFactory($config);
    }

    public function test_make_ocr_client(): void
    {
        $client = $this->factory->make('ocr');

        $this->assertInstanceOf(OcrClient::class, $client);
        $this->assertEquals('ocr', $client->getServiceName());
        $this->assertNull($client->getDefaultAccessKey());
    }

    public function test_make_ocr_client_with_access_key(): void
    {
        $client = $this->factory->make('ocr', 'default');

        $this->assertInstanceOf(OcrClient::class, $client);
        $this->assertEquals('ocr', $client->getServiceName());
        $this->assertEquals('default', $client->getDefaultAccessKey());
    }

    public function test_make_faceid_client(): void
    {
        $client = $this->factory->make('faceid');

        $this->assertInstanceOf(FaceIdClient::class, $client);
        $this->assertEquals('faceid', $client->getServiceName());
        $this->assertNull($client->getDefaultAccessKey());
    }

    public function test_make_faceid_client_with_access_key(): void
    {
        $client = $this->factory->make('faceid', 'production');

        $this->assertInstanceOf(FaceIdClient::class, $client);
        $this->assertEquals('faceid', $client->getServiceName());
        $this->assertEquals('production', $client->getDefaultAccessKey());
    }

    public function test_make_captcha_client(): void
    {
        $client = $this->factory->make('captcha');

        $this->assertInstanceOf(CaptchaClient::class, $client);
        $this->assertEquals('captcha', $client->getServiceName());
        $this->assertNull($client->getDefaultAccessKey());
    }

    public function test_make_captcha_client_with_access_key(): void
    {
        $client = $this->factory->make('captcha', 'default');

        $this->assertInstanceOf(CaptchaClient::class, $client);
        $this->assertEquals('captcha', $client->getServiceName());
        $this->assertEquals('default', $client->getDefaultAccessKey());
    }

    public function test_get_config(): void
    {
        $config = $this->factory->getConfig();

        $this->assertInstanceOf(Config::class, $config);
        $this->assertEquals('test_secret_id', $config->get('profiles.default.secret_id'));
    }

    public function test_get_logger(): void
    {
        $logger = $this->factory->getLogger();

        $this->assertInstanceOf(Logger::class, $logger);
    }

    public function test_get_http(): void
    {
        $http = $this->factory->getHttp();

        $this->assertInstanceOf(\Illuminate\Http\Client\Factory::class, $http);
    }
}
