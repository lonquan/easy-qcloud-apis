<?php

declare(strict_types=1);

namespace EasyQCloudApi\Support;

use EasyQCloudApi\Contracts\LoggerInterface;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

if (! function_exists('now')) {
    function now(): \Illuminate\Support\Carbon
    {
        return \Illuminate\Support\Carbon::now();
    }
}

/**
 * Laravel 日志实现
 */
class Logger implements LoggerInterface
{
    private ?PsrLoggerInterface $logger;

    public function __construct(?PsrLoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * 记录请求日志
     *
     * @param  string  $service  服务名称
     * @param  string  $action  接口名称
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     */
    public function logRequest(string $service, string $action, array $params, ?string $accessKey = null): void
    {
        $this->logger?->info('QCloud API Request', [
            'service' => $service,
            'action' => $action,
            'params' => $params,
            'access_key' => $accessKey,
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * 记录响应日志
     *
     * @param  string  $service  服务名称
     * @param  string  $action  接口名称
     * @param  array<string, mixed>  $response  响应数据
     * @param  int  $statusCode  HTTP状态码
     * @param  string|null  $accessKey  访问密钥标识
     */
    public function logResponse(
        string $service,
        string $action,
        array $response,
        int $statusCode,
        ?string $accessKey = null
    ): void {
        $this->logger?->info('QCloud API Response', [
            'service' => $service,
            'action' => $action,
            'response' => $response,
            'status_code' => $statusCode,
            'access_key' => $accessKey,
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    /**
     * 记录错误日志
     *
     * @param  string  $service  服务名称
     * @param  string  $action  接口名称
     * @param  string  $message  错误消息
     * @param  array<string, mixed>  $context  上下文信息
     * @param  string|null  $accessKey  访问密钥标识
     */
    public function logError(
        string $service,
        string $action,
        string $message,
        array $context = [],
        ?string $accessKey = null
    ): void {
        $this->logger?->error('QCloud API Error', [
            'service' => $service,
            'action' => $action,
            'message' => $message,
            'context' => $context,
            'access_key' => $accessKey,
            'timestamp' => now()->toDateTimeString(),
        ]);
    }
}
