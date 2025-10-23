<?php

declare(strict_types=1);

namespace EasyQCloudApi\Exceptions;

use Exception;

/**
 * 腾讯云 API 基础异常类
 */
class QCloudException extends Exception
{
    /**
     * 创建配置异常
     */
    public static function configurationError(string $message, ?\Throwable $previous = null): self
    {
        return new self("Configuration Error: {$message}", 0, $previous);
    }

    /**
     * 创建请求异常
     */
    public static function requestError(string $message, ?\Throwable $previous = null): self
    {
        return new self("Request Error: {$message}", 0, $previous);
    }

    /**
     * 创建签名异常
     */
    public static function signatureError(string $message, ?\Throwable $previous = null): self
    {
        return new self("Signature Error: {$message}", 0, $previous);
    }

    /**
     * 创建验证异常
     */
    public static function validationError(string $message, ?\Throwable $previous = null): self
    {
        return new self("Validation Error: {$message}", 0, $previous);
    }
}
