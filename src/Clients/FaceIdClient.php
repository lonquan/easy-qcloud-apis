<?php

declare(strict_types=1);

namespace EasyQCloudApi\Clients;

use EasyQCloudApi\Contracts\ConfigInterface;
use EasyQCloudApi\Contracts\LoggerInterface;
use Illuminate\Http\Client\Factory as HttpFactory;

/**
 * FaceID客户端
 */
class FaceIdClient extends BaseClient
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
        return 'faceid';
    }

    /**
     * 人脸核身
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function faceVerification(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('FaceVerification', $params, $accessKey);
    }

    /**
     * 身份信息核验
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function idCardVerification(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('IdCardVerification', $params, $accessKey);
    }

    /**
     * 银行卡信息核验
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function bankCardVerification(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('BankCardVerification', $params, $accessKey);
    }

    /**
     * 手机号在网时长核验
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function mobileNetworkTimeVerification(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('MobileNetworkTimeVerification', $params, $accessKey);
    }

    /**
     * 手机号在网状态核验
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function mobileStatusVerification(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('MobileStatusVerification', $params, $accessKey);
    }

    /**
     * 获取动作序列
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function getActionSequence(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('GetActionSequence', $params, $accessKey);
    }

    /**
     * 获取动作序列（增强版）
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function getActionSequenceEnhanced(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('GetActionSequenceEnhanced', $params, $accessKey);
    }

    /**
     * 获取动作序列V2
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function getActionSequenceV2(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('GetActionSequenceV2', $params, $accessKey);
    }

    /**
     * 获取动作序列V3
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function getActionSequenceV3(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('GetActionSequenceV3', $params, $accessKey);
    }

    /**
     * 获取动作序列V4
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function getActionSequenceV4(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('GetActionSequenceV4', $params, $accessKey);
    }

    /**
     * 获取动作序列V5
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function getActionSequenceV5(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('GetActionSequenceV5', $params, $accessKey);
    }

    /**
     * 获取动作序列V6
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function getActionSequenceV6(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('GetActionSequenceV6', $params, $accessKey);
    }

    /**
     * 获取动作序列V7
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function getActionSequenceV7(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('GetActionSequenceV7', $params, $accessKey);
    }

    /**
     * 获取动作序列V8
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function getActionSequenceV8(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('GetActionSequenceV8', $params, $accessKey);
    }

    /**
     * 获取动作序列V9
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function getActionSequenceV9(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('GetActionSequenceV9', $params, $accessKey);
    }

    /**
     * 获取动作序列V10
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function getActionSequenceV10(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('GetActionSequenceV10', $params, $accessKey);
    }

    /**
     * 获取动作序列V11
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function getActionSequenceV11(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('GetActionSequenceV11', $params, $accessKey);
    }

    /**
     * 获取动作序列V12
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function getActionSequenceV12(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('GetActionSequenceV12', $params, $accessKey);
    }

    /**
     * 获取动作序列V13
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function getActionSequenceV13(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('GetActionSequenceV13', $params, $accessKey);
    }

    /**
     * 获取动作序列V14
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function getActionSequenceV14(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('GetActionSequenceV14', $params, $accessKey);
    }

    /**
     * 获取动作序列V15
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function getActionSequenceV15(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('GetActionSequenceV15', $params, $accessKey);
    }

    /**
     * 获取动作序列V16
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function getActionSequenceV16(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('GetActionSequenceV16', $params, $accessKey);
    }

    /**
     * 获取动作序列V17
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function getActionSequenceV17(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('GetActionSequenceV17', $params, $accessKey);
    }

    /**
     * 获取动作序列V18
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function getActionSequenceV18(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('GetActionSequenceV18', $params, $accessKey);
    }

    /**
     * 获取动作序列V19
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function getActionSequenceV19(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('GetActionSequenceV19', $params, $accessKey);
    }

    /**
     * 获取动作序列V20
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function getActionSequenceV20(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('GetActionSequenceV20', $params, $accessKey);
    }
}
