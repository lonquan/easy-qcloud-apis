<?php

declare(strict_types=1);

namespace EasyQCloudApi\Contracts;

/**
 * 客户端接口
 */
interface ClientInterface
{
    /**
     * 发送请求
     *
     * @param  string  $action  接口名称
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识，可选
     * @return array<string, mixed> 响应数据
     *
     * @throws \EasyQCloudApi\Exceptions\RequestException
     */
    public function request(string $action, array $params = [], ?string $accessKey = null): array;

    /**
     * 获取服务名称
     */
    public function getServiceName(): string;

    /**
     * 获取默认访问密钥
     */
    public function getDefaultAccessKey(): ?string;
}
