<?php

declare(strict_types=1);

namespace EasyQCloudApi\Exceptions;

/**
 * 请求异常类
 */
class RequestException extends QCloudException
{
    protected int $statusCode;

    protected array $response;

    public function __construct(string $message, int $statusCode = 0, array $response = [], ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
        $this->statusCode = $statusCode;
        $this->response = $response;
    }

    /**
     * 获取HTTP状态码
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * 获取响应数据
     */
    public function getResponse(): array
    {
        return $this->response;
    }

    /**
     * 创建HTTP错误异常
     */
    public static function httpError(int $statusCode, string $message, array $response = []): self
    {
        return new self("HTTP Error {$statusCode}: {$message}", $statusCode, $response);
    }

    /**
     * 创建网络错误异常
     */
    public static function networkError(string $message, ?\Throwable $previous = null): self
    {
        return new self("Network Error: {$message}", 0, [], $previous);
    }
}
