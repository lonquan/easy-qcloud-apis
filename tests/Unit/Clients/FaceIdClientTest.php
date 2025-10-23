<?php

declare(strict_types=1);

namespace EasyQCloudApi\Tests\Unit\Clients;

use EasyQCloudApi\Clients\FaceIdClient;
use EasyQCloudApi\Contracts\ConfigInterface;
use EasyQCloudApi\Contracts\LoggerInterface;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\Response;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * FaceIdClient 测试类
 */
class FaceIdClientTest extends TestCase
{
    private FaceIdClient $client;

    private ConfigInterface $config;

    private LoggerInterface $logger;

    private HttpFactory $http;

    protected function setUp(): void
    {
        $this->config = Mockery::mock(ConfigInterface::class);
        $this->logger = Mockery::mock(LoggerInterface::class);
        $this->http = Mockery::mock(HttpFactory::class);

        $this->client = new FaceIdClient($this->config, $this->logger, $this->http, 'default');
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function test_get_service_name(): void
    {
        $this->assertEquals('faceid', $this->client->getServiceName());
    }

    public function test_get_default_access_key(): void
    {
        $this->assertEquals('default', $this->client->getDefaultAccessKey());
    }

    public function test_face_verification(): void
    {
        $params = ['IdCard' => '123456789012345678', 'Name' => 'Test User'];
        $expectedResponse = ['Response' => ['RequestId' => 'test']];

        $this->setupMockRequest('FaceVerification', $params, $expectedResponse);

        $result = $this->client->faceVerification($params);

        $this->assertEquals($expectedResponse, $result);
    }

    public function test_id_card_verification(): void
    {
        $params = ['IdCard' => '123456789012345678', 'Name' => 'Test User'];
        $expectedResponse = ['Response' => ['RequestId' => 'test']];

        $this->setupMockRequest('IdCardVerification', $params, $expectedResponse);

        $result = $this->client->idCardVerification($params);

        $this->assertEquals($expectedResponse, $result);
    }

    public function test_bank_card_verification(): void
    {
        $params = ['IdCard' => '123456789012345678', 'Name' => 'Test User', 'BankCard' => '1234567890123456'];
        $expectedResponse = ['Response' => ['RequestId' => 'test']];

        $this->setupMockRequest('BankCardVerification', $params, $expectedResponse);

        $result = $this->client->bankCardVerification($params);

        $this->assertEquals($expectedResponse, $result);
    }

    public function test_with_custom_access_key(): void
    {
        $params = ['IdCard' => '123456789012345678', 'Name' => 'Test User'];
        $expectedResponse = ['Response' => ['RequestId' => 'test']];

        $this->setupMockRequest('FaceVerification', $params, $expectedResponse, 'custom_key');

        $result = $this->client->faceVerification($params, 'custom_key');

        $this->assertEquals($expectedResponse, $result);
    }

    private function setupMockRequest(string $action, array $params, array $expectedResponse, ?string $accessKey = null): void
    {
        $this->config->shouldReceive('get')
            ->with('services.faceid', [])
            ->andReturn(['region' => 'ap-beijing', 'version' => '2018-03-01']);

        $this->config->shouldReceive('get')
            ->with('services.faceid.region', 'ap-beijing')
            ->andReturn('ap-beijing');

        $this->config->shouldReceive('get')
            ->with('services.faceid.version', '2018-05-22')
            ->andReturn('2018-03-01');

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
