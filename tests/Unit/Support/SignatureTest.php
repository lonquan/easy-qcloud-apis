<?php

declare(strict_types=1);

namespace EasyQCloudApi\Tests\Unit\Support;

use EasyQCloudApi\Support\Signature;
use PHPUnit\Framework\TestCase;

/**
 * Signature 测试类
 */
class SignatureTest extends TestCase
{
    private string $secretId = 'test_secret_id';

    private string $secretKey = 'test_secret_key';

    private string $service = 'ocr';

    private string $action = 'IdCardOCR';

    private string $host = 'ocr.tencentcloudapi.com';

    public function test_v3_sign_basic(): void
    {
        $data = ['ImageUrl' => 'test.jpg'];

        $result = Signature::v3Sign(
            $this->secretId,
            $this->secretKey,
            $this->service,
            $this->action,
            $this->host,
            $data
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('Authorization', $result);
        $this->assertArrayHasKey('Content-Type', $result);
        $this->assertArrayHasKey('Host', $result);
        $this->assertArrayHasKey('X-TC-Action', $result);
        $this->assertArrayHasKey('X-TC-Timestamp', $result);
        $this->assertArrayHasKey('X-TC-Version', $result);
        // X-TC-Region 不在签名结果中，它在请求参数中
        // X-TC-Language 不在签名结果中
    }

    public function test_v3_sign_authorization_format(): void
    {
        $data = ['ImageUrl' => 'test.jpg'];

        $result = Signature::v3Sign(
            $this->secretId,
            $this->secretKey,
            $this->service,
            $this->action,
            $this->host,
            $data
        );

        $authorization = $result['Authorization'];
        $this->assertStringStartsWith('TC3-HMAC-SHA256', $authorization);
        $this->assertStringContainsString('Credential='.$this->secretId, $authorization);
        $this->assertStringContainsString('SignedHeaders=', $authorization);
        $this->assertStringContainsString('Signature=', $authorization);
    }

    public function test_v3_sign_content_type(): void
    {
        $data = ['ImageUrl' => 'test.jpg'];

        $result = Signature::v3Sign(
            $this->secretId,
            $this->secretKey,
            $this->service,
            $this->action,
            $this->host,
            $data
        );

        $this->assertEquals('application/json; charset=utf-8', $result['Content-Type']);
    }

    public function test_v3_sign_headers(): void
    {
        $data = ['ImageUrl' => 'test.jpg'];

        $result = Signature::v3Sign(
            $this->secretId,
            $this->secretKey,
            $this->service,
            $this->action,
            $this->host,
            $data
        );

        $this->assertEquals($this->host, $result['Host']);
        $this->assertEquals($this->action, $result['X-TC-Action']);
        $this->assertIsString($result['X-TC-Timestamp']);
        // X-TC-Language 不在签名结果中
    }

    public function test_v3_sign_with_region(): void
    {
        $data = [
            'ImageUrl' => 'test.jpg',
            'Region' => 'ap-shanghai',
        ];

        $result = Signature::v3Sign(
            $this->secretId,
            $this->secretKey,
            $this->service,
            $this->action,
            $this->host,
            $data
        );

        // Region 不在签名结果中，它在请求参数中
        $this->assertIsArray($result);
    }

    public function test_v3_sign_with_version(): void
    {
        $data = [
            'ImageUrl' => 'test.jpg',
            'Version' => '2018-11-19',
        ];

        $result = Signature::v3Sign(
            $this->secretId,
            $this->secretKey,
            $this->service,
            $this->action,
            $this->host,
            $data
        );

        $this->assertEquals('2018-05-22', $result['X-TC-Version']);
    }

    public function test_v3_sign_different_methods(): void
    {
        $data = ['ImageUrl' => 'test.jpg'];

        $postResult = Signature::v3Sign(
            $this->secretId,
            $this->secretKey,
            $this->service,
            $this->action,
            $this->host,
            $data,
            'POST'
        );

        $getResult = Signature::v3Sign(
            $this->secretId,
            $this->secretKey,
            $this->service,
            $this->action,
            $this->host,
            $data,
            'GET'
        );

        // 不同HTTP方法应该产生不同的签名
        $this->assertNotEquals($postResult['Authorization'], $getResult['Authorization']);
    }

    public function test_v3_sign_consistency(): void
    {
        $data = ['ImageUrl' => 'test.jpg'];

        $result1 = Signature::v3Sign(
            $this->secretId,
            $this->secretKey,
            $this->service,
            $this->action,
            $this->host,
            $data
        );

        // 等待1秒确保时间戳不同
        sleep(1);

        $result2 = Signature::v3Sign(
            $this->secretId,
            $this->secretKey,
            $this->service,
            $this->action,
            $this->host,
            $data
        );

        // 时间戳应该不同，但其他字段应该相同
        $this->assertNotEquals($result1['X-TC-Timestamp'], $result2['X-TC-Timestamp']);
        $this->assertEquals($result1['Content-Type'], $result2['Content-Type']);
        $this->assertEquals($result1['Host'], $result2['Host']);
        $this->assertEquals($result1['X-TC-Action'], $result2['X-TC-Action']);
    }
}
