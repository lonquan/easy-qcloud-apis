<?php

declare(strict_types=1);

namespace EasyQCloudApi\Exceptions;

/**
 * 配置异常类
 */
class ConfigurationException extends QCloudException
{
    /**
     * 创建缺失配置异常
     */
    public static function missingConfiguration(string $key): self
    {
        return new self("Missing required configuration: {$key}");
    }

    /**
     * 创建无效配置异常
     */
    public static function invalidConfiguration(string $key, string $reason): self
    {
        return new self("Invalid configuration for '{$key}': {$reason}");
    }
}
