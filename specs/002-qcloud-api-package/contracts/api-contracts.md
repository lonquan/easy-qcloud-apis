# API Contracts: QCloud API Laravel Package

**Date**: 2024-12-19  
**Feature**: QCloud API Laravel Package  
**Purpose**: 定义包的公共 API 接口契约

## 核心接口

### QCloudFactory (工厂接口)

**职责**: 提供统一的客户端创建入口

```php
class QCloudFactory
{
    /**
     * 创建指定产品的客户端
     * 
     * @param string $product 产品名称 (ocr, faceid, captcha)
     * @param string|null $accessKey 可选的访问密钥
     * @return ClientInterface
     * @throws InvalidArgumentException
     */
    public static function make(string $product, ?string $accessKey = null): ClientInterface;
}
```

**使用示例**:
```php
// 使用默认 access_key
$ocrClient = QCloudFactory::make('ocr');

// 使用指定 access_key
$ocrClient = QCloudFactory::make('ocr', 'production');
```

### ClientInterface (客户端接口)

**职责**: 定义所有产品客户端的通用接口

```php
interface ClientInterface
{
    /**
     * 通用 API 请求方法
     * 
     * @param array $params 请求参数
     * @param string|null $accessKey 可选的访问密钥
     * @return array 响应数据
     * @throws RequestException
     */
    public function request(array $params, ?string $accessKey = null): array;
}
```

### OcrClient (OCR 客户端)

**职责**: 提供 OCR 相关的 API 方法

```php
class OcrClient implements ClientInterface
{
    use HttpClient;
    
    // 通用文字识别
    public function generalBasicOcr(array $params, ?string $accessKey = null): array;
    public function generalAccurateOcr(array $params, ?string $accessKey = null): array;
    
    // 卡证识别
    public function idCardOcr(array $params, ?string $accessKey = null): array;
    public function bankCardOcr(array $params, ?string $accessKey = null): array;
    public function bizLicenseOcr(array $params, ?string $accessKey = null): array;
    public function driverLicenseOcr(array $params, ?string $accessKey = null): array;
    public function vehicleLicenseOcr(array $params, ?string $accessKey = null): array;
    
    // 其他 OCR 接口
    public function businessCardOcr(array $params, ?string $accessKey = null): array;
    public function vehicleRegCertOcr(array $params, ?string $accessKey = null): array;
    public function mainlandPermitOcr(array $params, ?string $accessKey = null): array;
    public function hmtResidentPermitOcr(array $params, ?string $accessKey = null): array;
    public function hkIdCardOcr(array $params, ?string $accessKey = null): array;
    public function mlIdPassportOcr(array $params, ?string $accessKey = null): array;
    public function recognizeForeignPermanentResidentIdCard(array $params, ?string $accessKey = null): array;
    public function recognizeEncryptedIdCardOcr(array $params, ?string $accessKey = null): array;
    public function recognizeValidIdCardOcr(array $params, ?string $accessKey = null): array;
    public function recognizeGeneralCardWarn(array $params, ?string $accessKey = null): array;
    public function recognizeGeneralTextImageWarn(array $params, ?string $accessKey = null): array;
    public function recognizeTableAccurateOcr(array $params, ?string $accessKey = null): array;
    public function classifyStoreName(array $params, ?string $accessKey = null): array;
    public function recognizeStoreName(array $params, ?string $accessKey = null): array;
    public function classifyDetectOcr(array $params, ?string $accessKey = null): array;
}
```

### FaceIdClient (FaceID 客户端)

**职责**: 提供人脸核身相关的 API 方法

```php
class FaceIdClient implements ClientInterface
{
    use HttpClient;
    
    // 人脸核身 SaaS 服务
    public function checkEidTokenStatus(array $params, ?string $accessKey = null): array;
    public function detectAuth(array $params, ?string $accessKey = null): array;
    public function getDetectInfo(array $params, ?string $accessKey = null): array;
    public function getDetectInfoEnhanced(array $params, ?string $accessKey = null): array;
    public function getEidResult(array $params, ?string $accessKey = null): array;
    public function getEidToken(array $params, ?string $accessKey = null): array;
    public function getFaceIdResult(array $params, ?string $accessKey = null): array;
    public function getFaceIdToken(array $params, ?string $accessKey = null): array;
    
    // 实名信息核验
    public function idCardVerification(array $params, ?string $accessKey = null): array;
    public function idCardOcrVerification(array $params, ?string $accessKey = null): array;
    public function checkIdNameDate(array $params, ?string $accessKey = null): array;
    public function checkBankCardInformation(array $params, ?string $accessKey = null): array;
    public function bankCard2EVerification(array $params, ?string $accessKey = null): array;
    public function bankCardVerification(array $params, ?string $accessKey = null): array;
    public function bankCard4EVerification(array $params, ?string $accessKey = null): array;
    public function mobileNetworkTimeVerification(array $params, ?string $accessKey = null): array;
    public function mobileStatus(array $params, ?string $accessKey = null): array;
    public function phoneVerification(array $params, ?string $accessKey = null): array;
    public function phoneVerificationCmcc(array $params, ?string $accessKey = null): array;
    public function phoneVerificationCtcc(array $params, ?string $accessKey = null): array;
    public function phoneVerificationCucc(array $params, ?string $accessKey = null): array;
    public function checkPhoneAndName(array $params, ?string $accessKey = null): array;
    public function minorsVerification(array $params, ?string $accessKey = null): array;
    public function encryptedPhoneVerification(array $params, ?string $accessKey = null): array;
}
```

### CaptchaClient (Captcha 客户端)

**职责**: 提供验证码相关的 API 方法

```php
class CaptchaClient implements ClientInterface
{
    use HttpClient;
    
    // 验证码相关接口 (具体接口待腾讯云文档确认)
    public function describeCaptchaResult(array $params, ?string $accessKey = null): array;
    public function describeCaptchaUserAllAppId(array $params, ?string $accessKey = null): array;
    public function getCaptchaResult(array $params, ?string $accessKey = null): array;
    public function getTicketResult(array $params, ?string $accessKey = null): array;
}
```

## 支持接口

### ConfigInterface (配置接口)

**职责**: 定义配置管理接口

```php
interface ConfigInterface
{
    /**
     * 获取配置值
     */
    public function get(string $key, mixed $default = null): mixed;
    
    /**
     * 获取 Profile 配置
     */
    public function getProfile(string $name): array;
    
    /**
     * 获取 Service 配置
     */
    public function getService(string $name): array;
    
    /**
     * 验证配置
     */
    public function validate(): bool;
}
```

### LoggerInterface (日志接口)

**职责**: 定义日志记录接口

```php
interface LoggerInterface
{
    /**
     * 记录 API 请求日志
     */
    public function logRequest(string $service, array $params, ?string $accessKey = null): void;
    
    /**
     * 记录 API 响应日志
     */
    public function logResponse(string $service, array $response, int $duration): void;
    
    /**
     * 记录错误日志
     */
    public function logError(string $service, \Throwable $exception): void;
}
```

### HttpClient (HTTP 客户端 Trait)

**职责**: 提供通用的 HTTP 请求功能

```php
trait HttpClient
{
    protected const string ALGORITHM = 'TC3-HMAC-SHA256';
    protected const string CONTENT_TYPE = 'application/json';
    
    /**
     * 发送 HTTP 请求
     * 自动合并服务默认参数（包括 region）
     * 
     * @param array<string, mixed> $params 请求参数
     * @param string|null $accessKey 可选的访问密钥
     * @return array<string, mixed> 响应数据
     * @throws RequestException
     */
    protected function request(array $params, ?string $accessKey = null): array;
    
    /**
     * 获取服务默认参数
     * 包括 region 和 version
     * 
     * @return array<string, mixed> 默认参数数组
     */
    protected function getServiceDefaultParams(): array;
    
    /**
     * 获取服务默认地域
     * 
     * @return string|null 地域代码，如 'ap-guangzhou'
     */
    protected function getServiceRegion(): ?string;
    
    /**
     * 获取服务版本
     * 
     * @return string API版本，如 '2018-11-19'
     */
    protected function getServiceVersion(): string;
    
    /**
     * 获取服务主机地址
     * 
     * @return string 主机地址，如 'ocr.tencentcloudapi.com'
     */
    protected function getServiceHost(): string;
    
    /**
     * 获取服务完整 URL
     * 
     * @return string 完整的API URL
     */
    protected function getServiceUrl(): string;
    
    /**
     * 构建请求头
     * 包含 V3 签名
     * 
     * @param array<string, mixed> $params 请求参数
     * @param string|null $accessKey 可选的访问密钥
     * @return array<string, string> 请求头数组
     */
    protected function buildHeaders(array $params, ?string $accessKey): array;
    
    /**
     * V3 签名算法实现
     * 基于腾讯云官方规范
     * 
     * @param string $host 主机地址
     * @param array<string, string> $headers 请求头
     * @param string $queryString 查询字符串
     * @param array<string, mixed> $data 请求数据
     * @param string $method HTTP方法，默认为POST
     * @return array<string, string> 签名后的请求头
     */
    protected function v3Sign(
        string $host,
        array $headers,
        string $queryString,
        array $data,
        string $method = 'POST'
    ): array;
    
    /**
     * 记录请求日志
     * 
     * @param array<string, mixed> $params 请求参数
     * @param array<string, mixed> $response 响应数据
     * @return void
     */
    protected function logRequest(array $params, array $response): void;
}
```

## 异常接口

### QCloudException (基础异常)

```php
abstract class QCloudException extends \Exception
{
    protected string $service;
    protected ?string $accessKey;
    
    public function getService(): string;
    public function getAccessKey(): ?string;
}
```

### ConfigException (配置异常)

```php
class ConfigException extends QCloudException
{
    public static function missingProfile(string $name): self;
    public static function invalidCredentials(string $name): self;
    public static function missingService(string $name): self;
}
```

### RequestException (请求异常)

```php
class RequestException extends QCloudException
{
    protected array $response;
    
    public function getResponse(): array;
    public static function networkError(\Throwable $exception): self;
    public static function apiError(array $response): self;
    public static function signatureError(string $message): self;
}
```

## 使用示例

### 基本使用

```php
use EasyQCloudApi\QCloudFactory;

// 创建 OCR 客户端
$ocrClient = QCloudFactory::make('ocr');

// 身份证识别
$response = $ocrClient->idCardOcr([
    'ImageUrl' => 'https://example.com/id-card.jpg',
    'CardSide' => 'FRONT',
]);

// 使用指定 access_key
$response = $ocrClient->idCardOcr([
    'ImageUrl' => 'https://example.com/id-card.jpg',
], 'production');
```

### 通用请求

```php
// 使用通用 request 方法
$response = $ocrClient->request([
    'Action' => 'IDCardOCR',
    'Version' => '2018-11-19',
    'ImageUrl' => 'https://example.com/id-card.jpg',
]);
```

### 错误处理

```php
try {
    $response = $ocrClient->idCardOcr($params);
} catch (ConfigException $e) {
    // 配置错误
    Log::error('Config error: ' . $e->getMessage());
} catch (RequestException $e) {
    // 请求错误
    Log::error('Request error: ' . $e->getMessage());
} catch (QCloudException $e) {
    // 其他错误
    Log::error('QCloud error: ' . $e->getMessage());
}
```

## 扩展性

### 添加新产品

1. 创建新的客户端类
2. 实现 ClientInterface 接口
3. 使用 HttpClient trait
4. 在工厂方法中添加产品支持

### 添加新方法

1. 在对应的客户端类中添加方法
2. 遵循命名约定
3. 提供完整的类型提示
4. 添加相应的测试

## 向后兼容性

### 版本策略
- 0.x 版本允许破坏性变更
- 1.0+ 版本严格向后兼容
- 废弃功能至少保留一个主版本

### 迁移指导
- 提供详细的升级文档
- 标记废弃的功能
- 提供自动迁移工具（如可能）
