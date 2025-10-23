<?php

declare(strict_types=1);

namespace EasyQCloudApi\Clients;

use EasyQCloudApi\Contracts\ClientInterface;
use EasyQCloudApi\Contracts\ConfigInterface;
use EasyQCloudApi\Contracts\LoggerInterface;
use EasyQCloudApi\Support\HttpClient;
use Illuminate\Http\Client\Factory as HttpFactory;

/**
 * 基础客户端类
 */
abstract class BaseClient implements ClientInterface
{
    use HttpClient;

    protected string $serviceName;

    protected ?string $defaultAccessKey;

    public function __construct(
        ConfigInterface $config,
        LoggerInterface $logger,
        HttpFactory $http,
        ?string $defaultAccessKey = null
    ) {
        $this->config = $config;
        $this->logger = $logger;
        $this->http = $http;
        $this->defaultAccessKey = $defaultAccessKey;
        $this->serviceName = $this->getServiceName();
    }

    /**
     * 获取服务名称
     */
    abstract public function getServiceName(): string;

    /**
     * 获取默认访问密钥
     */
    public function getDefaultAccessKey(): ?string
    {
        return $this->defaultAccessKey;
    }
}
