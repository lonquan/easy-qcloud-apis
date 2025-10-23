<?php

declare(strict_types=1);

namespace EasyQCloudApi\Support;

/**
 * V3 签名算法实现
 */
class Signature
{
    private const ALGORITHM = 'TC3-HMAC-SHA256';

    private const CONTENT_TYPE = 'application/json; charset=utf-8';

    /**
     * 生成V3签名
     *
     * @param  string  $secretId  密钥ID
     * @param  string  $secretKey  密钥Key
     * @param  string  $service  服务名称
     * @param  string  $action  接口名称
     * @param  string  $host  主机名
     * @param  array<string, mixed>  $data  请求数据
     * @param  string  $method  HTTP方法
     * @return array<string, string> 签名头信息
     */
    public static function v3Sign(
        string $secretId,
        string $secretKey,
        string $service,
        string $action,
        string $host,
        array $data,
        string $method = 'POST'
    ): array {
        $timestamp = time();
        $date = gmdate('Y-m-d', $timestamp);

        // Step 1: 构建规范请求字符串
        $canonicalHeaders = implode("\n", [
            'content-type:'.self::CONTENT_TYPE,
            'host:'.$host,
            'x-tc-action:'.strtolower($action),
            '',
        ]);

        $signedHeaders = implode(';', [
            'content-type',
            'host',
            'x-tc-action',
        ]);

        $hashedRequestPayload = hash('SHA256', json_encode($data) ?: '{}');

        $canonicalRequest = $method."\n"
            .'/'."\n"
            .''."\n"  // query string
            .$canonicalHeaders."\n"
            .$signedHeaders."\n"
            .$hashedRequestPayload;

        // Step 2: 构建待签名字符串
        $credentialScope = $date.'/'.$service.'/tc3_request';
        $hashedCanonicalRequest = hash('SHA256', $canonicalRequest);
        $stringToSign = self::ALGORITHM."\n"
            .$timestamp."\n"
            .$credentialScope."\n"
            .$hashedCanonicalRequest;

        // Step 3: 计算签名
        $secretDate = hash_hmac('SHA256', $date, 'TC3'.$secretKey, true);
        $secretService = hash_hmac('SHA256', $service, $secretDate, true);
        $secretSigning = hash_hmac('SHA256', 'tc3_request', $secretService, true);
        $signature = hash_hmac('SHA256', $stringToSign, $secretSigning);

        // Step 4: 构建Authorization头
        $authorization = self::ALGORITHM
            .' Credential='.$secretId.'/'.$credentialScope
            .', SignedHeaders='.$signedHeaders
            .', Signature='.$signature;

        return [
            'Authorization' => $authorization,
            'Content-Type' => self::CONTENT_TYPE,
            'Host' => $host,
            'X-TC-Action' => $action,
            'X-TC-Timestamp' => (string) $timestamp,
            'X-TC-Version' => '2018-05-22',
        ];
    }
}
