<?php

declare(strict_types=1);

namespace EasyQCloudApi\Clients;

use EasyQCloudApi\Contracts\ConfigInterface;
use EasyQCloudApi\Contracts\LoggerInterface;
use Illuminate\Http\Client\Factory as HttpFactory;

/**
 * Captcha客户端
 */
class CaptchaClient extends BaseClient
{
    public function __construct(
        ConfigInterface $config,
        LoggerInterface $logger,
        HttpFactory $http,
        ?string $defaultAccessKey = null
    ) {
        parent::__construct($config, $logger, $http, $defaultAccessKey);
    }

    /**
     * 获取服务名称
     */
    public function getServiceName(): string
    {
        return 'captcha';
    }

    /**
     * 验证码校验
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function describeCaptchaResult(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('DescribeCaptchaResult', $params, $accessKey);
    }

    /**
     * 获取验证码
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function describeCaptchaData(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('DescribeCaptchaData', $params, $accessKey);
    }

    /**
     * 获取验证码统计
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function describeCaptchaDataSum(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('DescribeCaptchaDataSum', $params, $accessKey);
    }

    /**
     * 获取验证码AppId信息
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function describeCaptchaAppIdInfo(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('DescribeCaptchaAppIdInfo', $params, $accessKey);
    }

    /**
     * 获取验证码用户信息
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function describeCaptchaUserAllAppId(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('DescribeCaptchaUserAllAppId', $params, $accessKey);
    }

    /**
     * 获取验证码小程序信息
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function describeCaptchaMiniData(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('DescribeCaptchaMiniData', $params, $accessKey);
    }

    /**
     * 获取验证码小程序统计
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function describeCaptchaMiniDataSum(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('DescribeCaptchaMiniDataSum', $params, $accessKey);
    }

    /**
     * 获取验证码小程序AppId信息
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function describeCaptchaMiniAppIdInfo(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('DescribeCaptchaMiniAppIdInfo', $params, $accessKey);
    }

    /**
     * 获取验证码小程序用户信息
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function describeCaptchaMiniUserAllAppId(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('DescribeCaptchaMiniUserAllAppId', $params, $accessKey);
    }

    /**
     * 获取验证码小程序结果
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function describeCaptchaMiniResult(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('DescribeCaptchaMiniResult', $params, $accessKey);
    }

    /**
     * 获取验证码小程序结果
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function describeCaptchaMiniResultSum(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('DescribeCaptchaMiniResultSum', $params, $accessKey);
    }

    /**
     * 获取验证码小程序结果
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function describeCaptchaMiniResultSumV2(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('DescribeCaptchaMiniResultSumV2', $params, $accessKey);
    }

    /**
     * 获取验证码小程序结果
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function describeCaptchaMiniResultSumV3(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('DescribeCaptchaMiniResultSumV3', $params, $accessKey);
    }

    /**
     * 获取验证码小程序结果
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function describeCaptchaMiniResultSumV4(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('DescribeCaptchaMiniResultSumV4', $params, $accessKey);
    }

    /**
     * 获取验证码小程序结果
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function describeCaptchaMiniResultSumV5(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('DescribeCaptchaMiniResultSumV5', $params, $accessKey);
    }

    /**
     * 获取验证码小程序结果
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function describeCaptchaMiniResultSumV6(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('DescribeCaptchaMiniResultSumV6', $params, $accessKey);
    }

    /**
     * 获取验证码小程序结果
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function describeCaptchaMiniResultSumV7(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('DescribeCaptchaMiniResultSumV7', $params, $accessKey);
    }

    /**
     * 获取验证码小程序结果
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function describeCaptchaMiniResultSumV8(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('DescribeCaptchaMiniResultSumV8', $params, $accessKey);
    }

    /**
     * 获取验证码小程序结果
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function describeCaptchaMiniResultSumV9(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('DescribeCaptchaMiniResultSumV9', $params, $accessKey);
    }

    /**
     * 获取验证码小程序结果
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function describeCaptchaMiniResultSumV10(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('DescribeCaptchaMiniResultSumV10', $params, $accessKey);
    }
}
