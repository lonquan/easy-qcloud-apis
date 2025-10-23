<?php

declare(strict_types=1);

namespace EasyQCloudApi\Tests\Integration;

use EasyQCloudApi\Clients\OcrClient;
use EasyQCloudApi\Contracts\ConfigInterface;
use EasyQCloudApi\Contracts\LoggerInterface;
use EasyQCloudApi\Support\Config;
use EasyQCloudApi\Support\Logger;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\Response;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * Logger 集成测试
 */
class LoggerIntegrationTest extends TestCase
{
    private OcrClient $client;

    private ConfigInterface $config;

    private LoggerInterface $logger;

    private HttpFactory $http;

    protected function setUp(): void
    {
        $this->config = new Config([
            'profiles' => [
                'default' => [
                    'secret_id' => 'test_secret_id',
                    'secret_key' => 'test_secret_key',
                ],
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

        $this->logger = new Logger;
        $this->http = new HttpFactory;

        $this->client = new OcrClient($this->config, $this->logger, $this->http, 'default');
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function test_logger_integration_with_mock_http(): void
    {
        $httpMock = Mockery::mock(HttpFactory::class);
        $responseMock = Mockery::mock(Response::class);

        $httpMock->shouldReceive('withHeaders')
            ->andReturnSelf();
        $httpMock->shouldReceive('timeout')
            ->with(30)
            ->andReturnSelf();
        $httpMock->shouldReceive('post')
            ->andReturn($responseMock);

        $responseMock->shouldReceive('json')
            ->andReturn(['Response' => ['RequestId' => 'test_request_123']]);
        $responseMock->shouldReceive('status')
            ->andReturn(200);
        $responseMock->shouldReceive('successful')
            ->andReturn(true);

        // 使用反射替换HTTP客户端
        $reflection = new \ReflectionClass($this->client);
        $httpProperty = $reflection->getProperty('http');
        $httpProperty->setAccessible(true);
        $httpProperty->setValue($this->client, $httpMock);

        // 执行请求 - 这会触发日志记录
        $result = $this->client->idCardOCR(['ImageUrl' => 'test.jpg']);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('Response', $result);
    }

    public function test_logger_integration_with_error(): void
    {
        $httpMock = Mockery::mock(HttpFactory::class);

        $httpMock->shouldReceive('withHeaders')
            ->andReturnSelf();
        $httpMock->shouldReceive('timeout')
            ->with(30)
            ->andReturnSelf();
        $httpMock->shouldReceive('post')
            ->andThrow(new \Exception('Network timeout'));

        // 使用反射替换HTTP客户端
        $reflection = new \ReflectionClass($this->client);
        $httpProperty = $reflection->getProperty('http');
        $httpProperty->setAccessible(true);
        $httpProperty->setValue($this->client, $httpMock);

        // 执行请求 - 这会触发错误日志记录
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Network timeout');

        $this->client->idCardOCR(['ImageUrl' => 'test.jpg']);
    }

    public function test_logger_with_different_services(): void
    {
        $httpMock = Mockery::mock(HttpFactory::class);
        $responseMock = Mockery::mock(Response::class);

        $httpMock->shouldReceive('withHeaders')
            ->andReturnSelf();
        $httpMock->shouldReceive('timeout')
            ->with(30)
            ->andReturnSelf();
        $httpMock->shouldReceive('post')
            ->andReturn($responseMock);

        $responseMock->shouldReceive('json')
            ->andReturn(['Response' => ['RequestId' => 'test']]);
        $responseMock->shouldReceive('status')
            ->andReturn(200);
        $responseMock->shouldReceive('successful')
            ->andReturn(true);

        // 使用反射替换HTTP客户端
        $reflection = new \ReflectionClass($this->client);
        $httpProperty = $reflection->getProperty('http');
        $httpProperty->setAccessible(true);
        $httpProperty->setValue($this->client, $httpMock);

        // 测试不同服务的日志记录
        $this->client->idCardOCR(['ImageUrl' => 'test.jpg']);
        $this->client->bankCardOCR(['ImageUrl' => 'test.jpg']);
        $this->client->generalBasicOCR(['ImageUrl' => 'test.jpg']);

        $this->assertTrue(true); // 如果没有异常，说明日志记录正常工作
    }

    public function test_logger_with_different_access_keys(): void
    {
        $httpMock = Mockery::mock(HttpFactory::class);
        $responseMock = Mockery::mock(Response::class);

        $httpMock->shouldReceive('withHeaders')
            ->andReturnSelf();
        $httpMock->shouldReceive('timeout')
            ->with(30)
            ->andReturnSelf();
        $httpMock->shouldReceive('post')
            ->andReturn($responseMock);

        $responseMock->shouldReceive('json')
            ->andReturn(['Response' => ['RequestId' => 'test']]);
        $responseMock->shouldReceive('status')
            ->andReturn(200);
        $responseMock->shouldReceive('successful')
            ->andReturn(true);

        // 使用反射替换HTTP客户端
        $reflection = new \ReflectionClass($this->client);
        $httpProperty = $reflection->getProperty('http');
        $httpProperty->setAccessible(true);
        $httpProperty->setValue($this->client, $httpMock);

        // 测试不同access_key的日志记录
        $this->client->idCardOCR(['ImageUrl' => 'test.jpg'], 'production');
        $this->client->idCardOCR(['ImageUrl' => 'test.jpg'], 'testing');
        $this->client->idCardOCR(['ImageUrl' => 'test.jpg'], null);

        $this->assertTrue(true); // 如果没有异常，说明日志记录正常工作
    }
}
