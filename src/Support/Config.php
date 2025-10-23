<?php

declare(strict_types=1);

namespace EasyQCloudApi\Support;

use EasyQCloudApi\Contracts\ConfigInterface;
use EasyQCloudApi\Exceptions\ConfigurationException;
use Illuminate\Support\Arr;

/**
 * 配置管理实现
 */
class Config implements ConfigInterface
{
    private array $config = [];

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * 获取配置值
     *
     * @param  string  $key  配置键，支持点号分隔的嵌套键
     * @param  mixed  $default  默认值
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return Arr::get($this->config, $key, $default);
    }

    /**
     * 设置配置值
     *
     * @param  string  $key  配置键
     * @param  mixed  $value  配置值
     */
    public function set(string $key, mixed $value): void
    {
        Arr::set($this->config, $key, $value);
    }

    /**
     * 检查配置是否存在
     */
    public function has(string $key): bool
    {
        return Arr::has($this->config, $key);
    }

    /**
     * 获取所有配置
     *
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return $this->config;
    }

    /**
     * 验证配置
     *
     * @throws ConfigurationException
     */
    public function validate(): void
    {
        // 验证profiles配置
        $profiles = $this->get('profiles', []);
        if (empty($profiles)) {
            throw ConfigurationException::missingConfiguration('profiles');
        }

        foreach ($profiles as $accessKey => $profile) {
            $this->validateProfile($accessKey, $profile);
        }

        // 验证services配置
        $services = $this->get('services', []);
        if (empty($services)) {
            throw ConfigurationException::missingConfiguration('services');
        }

        foreach ($services as $serviceName => $serviceConfig) {
            $this->validateService($serviceName, $serviceConfig);
        }
    }

    /**
     * 验证单个profile配置
     *
     * @param  string  $accessKey  访问密钥标识
     * @param  array<string, mixed>  $profile  配置数据
     *
     * @throws ConfigurationException
     */
    private function validateProfile(string $accessKey, array $profile): void
    {
        if (empty($profile['secret_id'])) {
            throw ConfigurationException::invalidConfiguration(
                "profiles.{$accessKey}.secret_id",
                'secret_id is required and cannot be empty',
            );
        }

        if (empty($profile['secret_key'])) {
            throw ConfigurationException::invalidConfiguration(
                "profiles.{$accessKey}.secret_key",
                'secret_key is required and cannot be empty',
            );
        }

        if (! is_string($profile['secret_id'])) {
            throw ConfigurationException::invalidConfiguration(
                "profiles.{$accessKey}.secret_id",
                'secret_id must be a string',
            );
        }

        if (! is_string($profile['secret_key'])) {
            throw ConfigurationException::invalidConfiguration(
                "profiles.{$accessKey}.secret_key",
                'secret_key must be a string',
            );
        }
    }

    /**
     * 验证单个service配置
     *
     * @param  string  $serviceName  服务名称
     * @param  array<string, mixed>  $serviceConfig  配置数据
     *
     * @throws ConfigurationException
     */
    private function validateService(string $serviceName, array $serviceConfig): void
    {
        if (empty($serviceConfig['region'])) {
            throw ConfigurationException::invalidConfiguration(
                "services.{$serviceName}.region",
                'region is required and cannot be empty',
            );
        }

        if (! is_string($serviceConfig['region'])) {
            throw ConfigurationException::invalidConfiguration(
                "services.{$serviceName}.region",
                'region must be a string',
            );
        }

        // 验证version（如果存在）
        if (isset($serviceConfig['version']) && ! is_string($serviceConfig['version'])) {
            throw ConfigurationException::invalidConfiguration(
                "services.{$serviceName}.version",
                'version must be a string',
            );
        }

        // 验证defaults（如果存在）
        if (isset($serviceConfig['defaults']) && ! is_array($serviceConfig['defaults'])) {
            throw ConfigurationException::invalidConfiguration(
                "services.{$serviceName}.defaults",
                'defaults must be an array',
            );
        }
    }
}
