<?php

declare(strict_types=1);

namespace EasyQCloudApi;

use EasyQCloudApi\Clients\CaptchaClient;
use EasyQCloudApi\Clients\FaceIdClient;
use EasyQCloudApi\Clients\OcrClient;
use EasyQCloudApi\Contracts\ClientInterface;
use EasyQCloudApi\Contracts\ConfigInterface;
use EasyQCloudApi\Contracts\LoggerInterface;
use EasyQCloudApi\Exceptions\ConfigurationException;
use EasyQCloudApi\Support\Logger;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * 腾讯云客户端工厂
 */
class QCloudFactory
{
    private ?LoggerInterface $logger;

    private HttpFactory $http;

    private array $services = [
        'ocr' => OcrClient::class,
        'faceid' => FaceIdClient::class,
        'captcha' => CaptchaClient::class,
    ];

    public function __construct(private readonly ConfigInterface $config)
    {
        $this->logger = $this->resolveLogger();
        $this->http = $this->resolveHttp();
    }

    /**
     * 创建客户端实例
     *
     * @param  string  $service  服务名称 (ocr, faceid, captcha)
     * @param  string|null  $accessKey  访问密钥标识，可选
     * @return ClientInterface 客户端实例
     *
     * @throws ConfigurationException
     */
    public function make(string $service, ?string $accessKey = null): ClientInterface
    {
        $this->validateService($service);

        if (! isset($this->services[$service])) {
            $this->throwUnsupportedService($service);
        }

        $class = $this->services[$service];

        /** @var ClientInterface $client */
        $client = new $class(
            $this->getConfig(),
            $this->getLogger(),
            $this->getHttp(),
            $accessKey,
        );

        return $client;
    }

    /**
     * 验证服务配置
     *
     * @param  string  $service  服务名称
     *
     * @throws ConfigurationException
     */
    private function validateService(string $service): void
    {
        if (! $this->config->has("services.{$service}")) {
            throw ConfigurationException::missingConfiguration("services.{$service}");
        }
    }

    protected function resolveHttp(): HttpFactory
    {
        return new HttpFactory;
    }

    /**
     * 获取配置实例
     */
    public function getConfig(): ConfigInterface
    {
        return $this->config;
    }

    /**
     * 获取日志实例
     */
    public function getLogger(): ?LoggerInterface
    {
        return $this->logger;
    }

    /**
     * 获取 Http Client
     */
    public function getHttp(): HttpFactory
    {
        return $this->http;
    }

    /**
     * 解析日志器
     */
    private function resolveLogger(): LoggerInterface
    {
        $loggingConfig = $this->config->get('logging', []);

        if (! ($loggingConfig['enabled'] ?? true)) {
            return new Logger(null);
        }

        $channel = $loggingConfig['channel'] ?? 'default';

        try {
            return new Logger(Log::channel($channel));
        } catch (Throwable $e) {
            return new Logger(null);
        }
    }

    /**
     * @throws ConfigurationException
     */
    private function throwUnsupportedService(string $service): never
    {
        throw ConfigurationException::invalidConfiguration(
            'service',
            "Unsupported service: {$service}",
        );
    }
}
