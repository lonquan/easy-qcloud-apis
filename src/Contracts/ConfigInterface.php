<?php

declare(strict_types=1);

namespace EasyQCloudApi\Contracts;

/**
 * 配置接口
 */
interface ConfigInterface
{
    /**
     * 获取配置值
     *
     * @param  string  $key  配置键，支持点号分隔的嵌套键
     * @param  mixed  $default  默认值
     */
    public function get(string $key, mixed $default = null): mixed;

    /**
     * 设置配置值
     *
     * @param  string  $key  配置键
     * @param  mixed  $value  配置值
     */
    public function set(string $key, mixed $value): void;

    /**
     * 检查配置是否存在
     */
    public function has(string $key): bool;

    /**
     * 获取所有配置
     *
     * @return array<string, mixed>
     */
    public function all(): array;

    /**
     * 验证配置
     *
     * @throws \EasyQCloudApi\Exceptions\ConfigurationException
     */
    public function validate(): void;
}
