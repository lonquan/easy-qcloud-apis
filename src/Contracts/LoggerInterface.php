<?php

declare(strict_types=1);

namespace EasyQCloudApi\Contracts;

/**
 * 日志接口
 */
interface LoggerInterface
{
    /**
     * 记录请求日志
     *
     * @param  string  $service  服务名称
     * @param  string  $action  接口名称
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     */
    public function logRequest(string $service, string $action, array $params, ?string $accessKey = null): void;

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
    ): void;

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
    ): void;
}
