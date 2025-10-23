<?php

declare(strict_types=1);

namespace EasyQCloudApi\Tests\Unit\Clients;

use EasyQCloudApi\Clients\CaptchaClient;
use EasyQCloudApi\Contracts\ConfigInterface;
use EasyQCloudApi\Contracts\LoggerInterface;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\Response;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * CaptchaClient 测试类
 */
class CaptchaClientTest extends TestCase
{
    private CaptchaClient $client;

    private ConfigInterface $config;

    private LoggerInterface $logger;

    private HttpFactory $http;

    protected function setUp(): void
    {
        $this->config = Mockery::mock(ConfigInterface::class);
        $this->logger = Mockery::mock(LoggerInterface::class);
        $this->http = Mockery::mock(HttpFactory::class);

        $this->client = new CaptchaClient($this->config, $this->logger, $this->http, 'default');
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function test_get_service_name(): void
    {
        $this->assertEquals('captcha', $this->client->getServiceName());
    }

    public function test_get_default_access_key(): void
    {
        $this->assertEquals('default', $this->client->getDefaultAccessKey());
    }

    public function test_describe_captcha_result(): void
    {
        $params = ['CaptchaType' => 1, 'UserIp' => '127.0.0.1'];
        $expectedResponse = ['Response' => ['RequestId' => 'test']];

        $this->setupMockRequest('DescribeCaptchaResult', $params, $expectedResponse);

        $result = $this->client->describeCaptchaResult($params);

        $this->assertEquals($expectedResponse, $result);
    }

    public function test_describe_captcha_data(): void
    {
        $params = ['CaptchaType' => 1];
        $expectedResponse = ['Response' => ['RequestId' => 'test']];

        $this->setupMockRequest('DescribeCaptchaData', $params, $expectedResponse);

        $result = $this->client->describeCaptchaData($params);

        $this->assertEquals($expectedResponse, $result);
    }

    public function test_describe_captcha_data_sum(): void
    {
        $params = ['CaptchaType' => 1];
        $expectedResponse = ['Response' => ['RequestId' => 'test']];

        $this->setupMockRequest('DescribeCaptchaDataSum', $params, $expectedResponse);

        $result = $this->client->describeCaptchaDataSum($params);

        $this->assertEquals($expectedResponse, $result);
    }

    public function test_with_custom_access_key(): void
    {
        $params = ['CaptchaType' => 1];
        $expectedResponse = ['Response' => ['RequestId' => 'test']];

        $this->setupMockRequest('DescribeCaptchaResult', $params, $expectedResponse, 'custom_key');

        $result = $this->client->describeCaptchaResult($params, 'custom_key');

        $this->assertEquals($expectedResponse, $result);
    }

    private function setupMockRequest(string $action, array $params, array $expectedResponse, ?string $accessKey = null): void
    {
        $this->config->shouldReceive('get')
            ->with('services.captcha', [])
            ->andReturn(['region' => 'ap-shanghai', 'version' => '2019-07-22']);

        $this->config->shouldReceive('get')
            ->with('services.captcha.region', 'ap-beijing')
            ->andReturn('ap-shanghai');

        $this->config->shouldReceive('get')
            ->with('services.captcha.version', '2018-05-22')
            ->andReturn('2019-07-22');

        $this->config->shouldReceive('get')
            ->with('profiles.'.($accessKey ?? 'default'))
            ->andReturn(['secret_id' => 'test_id', 'secret_key' => 'test_key']);

        $this->config->shouldReceive('get')
            ->with('request.timeout', 30)
            ->andReturn(30);

        $this->logger->shouldReceive('logRequest')->once();
        $this->logger->shouldReceive('logResponse')->once();

        $response = Mockery::mock(Response::class);
        $response->shouldReceive('json')->andReturn($expectedResponse);
        $response->shouldReceive('status')->andReturn(200);
        $response->shouldReceive('successful')->andReturn(true);

        $this->http->shouldReceive('withHeaders')
            ->andReturnSelf();
        $this->http->shouldReceive('timeout')
            ->with(30)
            ->andReturnSelf();
        $this->http->shouldReceive('post')
            ->andReturn($response);
    }
}
