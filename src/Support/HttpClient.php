<?php

declare(strict_types=1);

namespace EasyQCloudApi\Support;

use EasyQCloudApi\Contracts\ConfigInterface;
use EasyQCloudApi\Contracts\LoggerInterface;
use EasyQCloudApi\Exceptions\QCloudException;
use EasyQCloudApi\Exceptions\RequestException;
use Illuminate\Http\Client\Factory as HttpFactory;

/**
 * HTTP客户端trait
 */
trait HttpClient
{
    protected ConfigInterface $config;

    protected LoggerInterface $logger;

    protected HttpFactory $http;

    /**
     * 发送HTTP请求
     *
     * @param  string  $action  接口名称
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     *
     * @throws QCloudException
     * @throws RequestException
     */
    public function request(string $action, array $params = [], ?string $accessKey = null): array
    {
        $serviceName = $this->getServiceName();
        $accessKey = $accessKey ?? $this->getDefaultAccessKey();

        if (! $accessKey) {
            throw RequestException::requestError('No access key provided');
        }

        // 获取服务配置
        $serviceConfig = $this->getServiceConfig($serviceName);
        $region = $this->getServiceRegion($serviceName);
        $version = $this->getServiceVersion($serviceName);
        $host = $this->getServiceHost($serviceName, $region);

        // 合并参数
        $mergedParams = $this->mergeParams($params, $serviceConfig, $region, $version);

        // 记录请求日志
        $this->logRequest($serviceName, $action, $mergedParams, $accessKey);

        try {
            // 构建请求头
            $headers = $this->buildHeaders($action, $host, $mergedParams, $accessKey);

            // 获取超时配置
            $timeout = $this->config->get('request.timeout', 30);

            // 发送请求
            $response = $this->http->withHeaders($headers)
                ->timeout($timeout)
                ->post($this->getServiceUrl($serviceName, $region), $mergedParams);

            // 记录响应日志
            $this->logResponse($serviceName, $action, $response->json() ?? [], $response->status(), $accessKey);

            if (! $response->successful()) {
                throw RequestException::httpError(
                    $response->status(),
                    $response->body(),
                    $response->json() ?? [],
                );
            }

            return $response->json() ?? [];

        } catch (\Exception $e) {
            $this->logError($serviceName, $action, $e->getMessage(), ['exception' => $e], $accessKey);

            throw $e;
        }
    }

    /**
     * 构建请求头
     *
     * @param  string  $action  接口名称
     * @param  string  $host  主机名
     * @param  array<string, mixed>  $data  请求数据
     * @param  string  $accessKey  访问密钥标识
     * @return array<string, string> 请求头
     *
     * @throws QCloudException
     */
    protected function buildHeaders(string $action, string $host, array $data, string $accessKey): array
    {
        $profile = $this->config->get("profiles.{$accessKey}");
        if (! $profile) {
            throw RequestException::requestError("Profile not found for access key: {$accessKey}");
        }

        $serviceName = $this->getServiceName();

        return Signature::v3Sign(
            $profile['secret_id'],
            $profile['secret_key'],
            $serviceName,
            $action,
            $host,
            $data,
        );
    }

    /**
     * 获取服务配置
     *
     * @param  string  $serviceName  服务名称
     * @return array<string, mixed> 服务配置
     */
    protected function getServiceConfig(string $serviceName): array
    {
        return $this->config->get("services.{$serviceName}", []);
    }

    /**
     * 获取服务区域
     *
     * @param  string  $serviceName  服务名称
     * @return string 区域
     */
    protected function getServiceRegion(string $serviceName): string
    {
        return $this->config->get("services.{$serviceName}.region", 'ap-beijing');
    }

    /**
     * 获取服务版本
     *
     * @param  string  $serviceName  服务名称
     * @return string 版本
     */
    protected function getServiceVersion(string $serviceName): string
    {
        return $this->config->get("services.{$serviceName}.version", '2018-05-22');
    }

    /**
     * 获取服务主机
     *
     * @param  string  $serviceName  服务名称
     * @param  string  $region  区域
     * @return string 主机名
     */
    protected function getServiceHost(string $serviceName, string $region): string
    {
        return "{$serviceName}.tencentcloudapi.com";
    }

    /**
     * 获取服务URL
     *
     * @param  string  $serviceName  服务名称
     * @param  string  $region  区域
     * @return string URL
     */
    protected function getServiceUrl(string $serviceName, string $region): string
    {
        return "https://{$this->getServiceHost($serviceName, $region)}/";
    }

    /**
     * 合并参数
     *
     * @param  array<string, mixed>  $userParams  用户参数
     * @param  array<string, mixed>  $serviceConfig  服务配置
     * @param  string  $region  区域
     * @param  string  $version  版本
     * @return array<string, mixed> 合并后的参数
     */
    protected function mergeParams(array $userParams, array $serviceConfig, string $region, string $version): array
    {
        $defaultParams = [
            'Region' => $region,
            'Version' => $version,
        ];

        // 合并服务默认参数
        $serviceDefaults = $serviceConfig['defaults'] ?? [];

        return array_merge($defaultParams, $serviceDefaults, $userParams);
    }

    /**
     * 记录请求日志
     *
     * @param  string  $service  服务名称
     * @param  string  $action  接口名称
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     */
    protected function logRequest(string $service, string $action, array $params, ?string $accessKey = null): void
    {
        $this->logger->logRequest($service, $action, $params, $accessKey);
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
    protected function logResponse(
        string $service,
        string $action,
        array $response,
        int $statusCode,
        ?string $accessKey = null
    ): void {
        $this->logger->logResponse($service, $action, $response, $statusCode, $accessKey);
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
    protected function logError(
        string $service,
        string $action,
        string $message,
        array $context = [],
        ?string $accessKey = null
    ): void {
        $this->logger->logError($service, $action, $message, $context, $accessKey);
    }
}
