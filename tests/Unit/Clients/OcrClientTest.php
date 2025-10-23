<?php

declare(strict_types=1);

namespace EasyQCloudApi\Tests\Unit\Clients;

use EasyQCloudApi\Clients\OcrClient;
use EasyQCloudApi\Contracts\ConfigInterface;
use EasyQCloudApi\Contracts\LoggerInterface;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\Response;
use Mockery;
use PHPUnit\Framework\TestCase;

/**
 * OcrClient 测试类
 */
class OcrClientTest extends TestCase
{
    private OcrClient $client;

    private ConfigInterface $config;

    private LoggerInterface $logger;

    private HttpFactory $http;

    protected function setUp(): void
    {
        $this->config = Mockery::mock(ConfigInterface::class);
        $this->logger = Mockery::mock(LoggerInterface::class);
        $this->http = Mockery::mock(HttpFactory::class);

        $this->client = new OcrClient($this->config, $this->logger, $this->http, 'default');
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }

    public function test_get_service_name(): void
    {
        $this->assertEquals('ocr', $this->client->getServiceName());
    }

    public function test_get_default_access_key(): void
    {
        $this->assertEquals('default', $this->client->getDefaultAccessKey());
    }

    public function test_id_card_ocr(): void
    {
        $params = ['ImageUrl' => 'test.jpg'];
        $expectedResponse = ['Response' => ['RequestId' => 'test']];

        $this->setupMockRequest('IdCardOCR', $params, $expectedResponse);

        $result = $this->client->idCardOCR($params);

        $this->assertEquals($expectedResponse, $result);
    }

    public function test_bank_card_ocr(): void
    {
        $params = ['ImageUrl' => 'test.jpg'];
        $expectedResponse = ['Response' => ['RequestId' => 'test']];

        $this->setupMockRequest('BankCardOCR', $params, $expectedResponse);

        $result = $this->client->bankCardOCR($params);

        $this->assertEquals($expectedResponse, $result);
    }

    public function test_general_basic_ocr(): void
    {
        $params = ['ImageUrl' => 'test.jpg'];
        $expectedResponse = ['Response' => ['RequestId' => 'test']];

        $this->setupMockRequest('GeneralBasicOCR', $params, $expectedResponse);

        $result = $this->client->generalBasicOCR($params);

        $this->assertEquals($expectedResponse, $result);
    }

    public function test_driver_license_ocr(): void
    {
        $params = ['ImageUrl' => 'test.jpg'];
        $expectedResponse = ['Response' => ['RequestId' => 'test']];

        $this->setupMockRequest('DriverLicenseOCR', $params, $expectedResponse);

        $result = $this->client->driverLicenseOCR($params);

        $this->assertEquals($expectedResponse, $result);
    }

    public function test_vehicle_license_ocr(): void
    {
        $params = ['ImageUrl' => 'test.jpg'];
        $expectedResponse = ['Response' => ['RequestId' => 'test']];

        $this->setupMockRequest('VehicleLicenseOCR', $params, $expectedResponse);

        $result = $this->client->vehicleLicenseOCR($params);

        $this->assertEquals($expectedResponse, $result);
    }

    public function test_biz_license_ocr(): void
    {
        $params = ['ImageUrl' => 'test.jpg'];
        $expectedResponse = ['Response' => ['RequestId' => 'test']];

        $this->setupMockRequest('BizLicenseOCR', $params, $expectedResponse);

        $result = $this->client->bizLicenseOCR($params);

        $this->assertEquals($expectedResponse, $result);
    }

    public function test_with_custom_access_key(): void
    {
        $params = ['ImageUrl' => 'test.jpg'];
        $expectedResponse = ['Response' => ['RequestId' => 'test']];

        $this->setupMockRequest('IdCardOCR', $params, $expectedResponse, 'custom_key');

        $result = $this->client->idCardOCR($params, 'custom_key');

        $this->assertEquals($expectedResponse, $result);
    }

    public function test_request_exception(): void
    {
        $params = ['ImageUrl' => 'test.jpg'];

        $this->config->shouldReceive('get')
            ->with('services.ocr', [])
            ->andReturn(['region' => 'ap-beijing', 'version' => '2018-11-19']);

        $this->config->shouldReceive('get')
            ->with('services.ocr.region', 'ap-beijing')
            ->andReturn('ap-beijing');

        $this->config->shouldReceive('get')
            ->with('services.ocr.version', '2018-05-22')
            ->andReturn('2018-11-19');

        $this->config->shouldReceive('get')
            ->with('profiles.default')
            ->andReturn(['secret_id' => 'test_id', 'secret_key' => 'test_key']);

        $this->config->shouldReceive('get')
            ->with('request.timeout', 30)
            ->andReturn(30);

        $this->logger->shouldReceive('logRequest')->once();
        $this->logger->shouldReceive('logError')->once();

        $this->http->shouldReceive('withHeaders')
            ->andThrow(new \Exception('Network error'));

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Network error');

        $this->client->idCardOCR($params);
    }

    private function setupMockRequest(string $action, array $params, array $expectedResponse, ?string $accessKey = null): void
    {
        $this->config->shouldReceive('get')
            ->with('services.ocr', [])
            ->andReturn(['region' => 'ap-beijing', 'version' => '2018-11-19']);

        $this->config->shouldReceive('get')
            ->with('services.ocr.region', 'ap-beijing')
            ->andReturn('ap-beijing');

        $this->config->shouldReceive('get')
            ->with('services.ocr.version', '2018-05-22')
            ->andReturn('2018-11-19');

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
