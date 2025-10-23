<?php

declare(strict_types=1);

namespace EasyQCloudApi\Tests\Integration;

use EasyQCloudApi\Clients\OcrClient;
use EasyQCloudApi\QCloudFactory;
use EasyQCloudApi\Support\Config;
use EasyQCloudApi\Support\Logger;
use PHPUnit\Framework\TestCase;

/**
 * QCloudFactory 集成测试
 */
class QCloudFactoryTest extends TestCase
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
            ],
            'services' => [
                'ocr' => [
                    'region' => 'ap-beijing',
                    'version' => '2018-05-22',
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
    }

    public function test_make_ocr_client_with_access_key(): void
    {
        $client = $this->factory->make('ocr', 'default');

        $this->assertInstanceOf(OcrClient::class, $client);
        $this->assertEquals('ocr', $client->getServiceName());
        $this->assertEquals('default', $client->getDefaultAccessKey());
    }

    public function test_get_config(): void
    {
        $config = $this->factory->getConfig();

        $this->assertInstanceOf(Config::class, $config);
    }

    public function test_get_logger(): void
    {
        $logger = $this->factory->getLogger();

        $this->assertInstanceOf(Logger::class, $logger);
    }
}
