# Research: QCloud API Laravel Package

**Date**: 2024-12-19  
**Feature**: QCloud API Laravel Package  
**Purpose**: 研究腾讯云 API 集成、Laravel Package 开发最佳实践和技术选型

## 技术选型研究

### 腾讯云 V3 签名算法实现

**Decision**: 使用原生 PHP 实现 TC3-HMAC-SHA256 签名算法，参考现有实现

**Rationale**: 
- 腾讯云官方要求使用 TC3-HMAC-SHA256 签名方法
- 避免引入额外的签名库依赖，减少包体积
- 签名算法相对固定，不需要频繁更新
- 基于已验证的参考实现，确保正确性

**Alternatives considered**:
- 使用第三方签名库：增加依赖复杂度
- 使用腾讯云官方 SDK：过于庞大，不符合轻量级包的需求

**Implementation approach**:
```php
// 基于参考实现的 V3 签名算法
protected function v3Sign(
    string $host,
    array $headers,
    string $queryString,
    array $data,
    string $method = 'POST'
): array {
    // Step 1: 构建规范请求字符串
    $action = $headers['Action'] ?? '';
    $canonicalHeaders = implode("\n", [
        'content-type:' . $this->contentType,
        'host:' . $host,
        'x-tc-action:' . strtolower($action),
        '',
    ]);
    $signedHeaders = implode(';', [
        'content-type',
        'host', 
        'x-tc-action',
    ]);
    
    $hashedRequestPayload = hash('SHA256', json_encode($data) ?: '{}');
    $canonicalRequest = $method . "\n"
        . '/' . "\n"
        . $queryString . "\n"
        . $canonicalHeaders . "\n"
        . $signedHeaders . "\n"
        . $hashedRequestPayload;
    
    // Step 2: 构建待签名字符串
    $timestamp = time();
    $date = gmdate('Y-m-d', $timestamp);
    [$service] = explode('.', $host);
    
    $credentialScope = $date . '/' . $service . '/tc3_request';
    $hashedCanonicalRequest = hash('SHA256', $canonicalRequest);
    $stringToSign = $this->algorithm . "\n"
        . $timestamp . "\n"
        . $credentialScope . "\n"
        . $hashedCanonicalRequest;
    
    // Step 3: 计算签名
    $secretDate = hash_hmac('SHA256', $date, 'TC3' . $this->secretKey, true);
    $secretService = hash_hmac('SHA256', $service, $secretDate, true);
    $secretSigning = hash_hmac('SHA256', 'tc3_request', $secretService, true);
    $signature = hash_hmac('SHA256', $stringToSign, $secretSigning);
    
    // Step 4: 构建 Authorization 头部
    $authorization = $this->algorithm
        . ' Credential=' . $this->secretId . '/' . $credentialScope
        . ', SignedHeaders=' . $signedHeaders . ', Signature=' . $signature;
    
    return [
        'Authorization' => $authorization,
        'Content-Type' => $this->contentType,
        'Host' => $host,
        'X-TC-Action' => $action,
        'X-TC-Timestamp' => $timestamp,
        'X-TC-Version' => $headers['Version'] ?? '',
        'X-TC-Region' => $headers['Region'] ?? '',
        'X-TC-Language' => 'zh-CN',
    ];
}
```

### Laravel HTTP 客户端集成

**Decision**: 使用 Laravel Http Facade 进行 HTTP 请求

**Rationale**:
- 深度集成 Laravel 生态系统
- 支持 Laravel 的中间件、日志、重试等功能
- 统一的错误处理和响应格式

**Alternatives considered**:
- 直接使用 Guzzle：缺少 Laravel 集成
- 使用 Symfony HTTP Client：需要额外配置

**Implementation approach**:
```php
use Illuminate\Support\Facades\Http;

$response = Http::withHeaders($headers)
    ->post($url, $data)
    ->throw();
```

### 配置管理策略

**Decision**: 使用 Laravel 配置系统，支持多 profile 和环境变量

**Rationale**:
- 符合 Laravel 最佳实践
- 支持不同环境配置
- 便于部署和维护

**Alternatives considered**:
- 硬编码配置：不灵活
- 数据库存储：过度复杂

**Implementation approach**:
```php
// 配置文件结构
'default' => 'production',
'profiles' => [
    'production' => [
        'secret_id' => env('QCLOUD_SECRET_ID'),
        'secret_key' => env('QCLOUD_SECRET_KEY'),
        'language' => 'zh-CN',
    ],
    'testing' => [
        'secret_id' => env('QCLOUD_TEST_SECRET_ID'),
        'secret_key' => env('QCLOUD_TEST_SECRET_KEY'),
        'language' => 'zh-CN',
    ],
],
'services' => [
    'ocr' => [
        'domain' => 'ocr.tencentcloudapi.com',
        'version' => '2018-11-19',
        'region' => 'ap-guangzhou', // 服务默认地域
    ],
    'faceid' => [
        'domain' => 'faceid.tencentcloudapi.com',
        'version' => '2018-03-01',
        'region' => 'ap-beijing', // 不同服务可配置不同地域
    ],
],
```

### 日志记录策略

**Decision**: 集成 Laravel 日志系统，记录到指定频道

**Rationale**:
- 利用 Laravel 现有日志基础设施
- 支持多种日志驱动（文件、数据库、云服务等）
- 便于日志分析和监控

**Alternatives considered**:
- 自定义日志文件：重复造轮子
- 第三方日志服务：增加复杂度

**Implementation approach**:
```php
Log::channel('qcloud')->info('API Request', [
    'service' => 'ocr',
    'action' => 'IDCardOCR',
    'access_key' => 'masked_key',
    'request' => $requestData,
    'response' => $responseData,
    'timestamp' => now(),
]);
```

### 错误处理策略

**Decision**: 提供基本的错误处理，不包含自动重试

**Rationale**:
- 符合用户需求，只关心 API 调用成功与否
- 避免复杂的重试逻辑
- 让用户自行处理业务逻辑

**Alternatives considered**:
- 自动重试机制：增加复杂度
- 详细错误分析：超出包的范围

**Implementation approach**:
```php
try {
    $response = $client->request($params);
    return $response;
} catch (RequestException $e) {
    Log::error('QCloud API Error', [
        'error' => $e->getMessage(),
        'code' => $e->getCode(),
    ]);
    throw $e;
}
```

### 产品客户端设计

**Decision**: 使用 HttpClient trait 提供通用 HTTP 请求功能，各产品客户端实现特定接口

**Rationale**:
- 使用 trait 避免继承的复杂性
- 更灵活的代码复用
- 符合组合优于继承的原则
- 便于测试和模拟

**Alternatives considered**:
- 继承基础客户端：增加继承层次复杂度
- 单一客户端处理所有产品：过于复杂
- 动态方法生成：类型安全性差

**Implementation approach**:
```php
// HTTP 客户端 trait
trait HttpClient
{
    protected const string ALGORITHM = 'TC3-HMAC-SHA256';
    protected const string CONTENT_TYPE = 'application/json';
    
    protected function request(array $params, ?string $accessKey = null): array
    {
        // 1. 合并服务默认参数（包括 region）
        $defaultParams = $this->getServiceDefaultParams();
        $params = array_merge($defaultParams, $params);
        
        // 2. 构建请求头和签名
        $headers = $this->buildHeaders($params, $accessKey);
        
        // 3. 发送 HTTP 请求
        $response = Http::withHeaders($headers)
            ->post($this->getServiceUrl(), $params)
            ->throw();
            
        // 4. 记录日志
        $this->logRequest($params, $response->json());
        
        return $response->json();
    }
    
    protected function getServiceDefaultParams(): array
    {
        // 获取服务默认参数，包括 region
        return [
            'Region' => $this->getServiceRegion(),
            'Version' => $this->getServiceVersion(),
        ];
    }
    
    protected function buildHeaders(array $params, ?string $accessKey): array
    {
        $action = $params['Action'] ?? '';
        $version = $params['Version'] ?? '';
        $region = $params['Region'] ?? '';
        
        $headers = [
            'Action' => $action,
            'Version' => $version,
            'Region' => $region,
        ];
        
        // 使用 V3 签名算法
        return $this->v3Sign(
            $this->getServiceHost(),
            $headers,
            '', // query string
            $params
        );
    }
    
    protected function v3Sign(
        string $host,
        array $headers,
        string $queryString,
        array $data,
        string $method = 'POST'
    ): array {
        // 基于参考实现的完整 V3 签名算法
        // (实现细节见上面的完整代码)
    }
}

// OCR 客户端
class OcrClient implements ClientInterface
{
    use HttpClient;
    
    public function idCardOcr(array $params, ?string $accessKey = null): array
    {
        return $this->request(array_merge($params, [
            'Action' => 'IDCardOCR',
        ]), $accessKey);
    }
}
```

### Region 参数处理策略

**Decision**: Region 配置在 services 中，自动合并到请求参数，支持用户覆盖

**Rationale**:
- 按产品配置不同地域更合理
- 用户可以通过输入参数覆盖默认 region
- 符合腾讯云 API 设计理念

**Implementation approach**:
```php
// 1. 服务配置中的 region 作为默认值
'services' => [
    'ocr' => [
        'region' => 'ap-guangzhou', // 默认地域
    ],
],

// 2. 请求时自动合并
$params = [
    'ImageUrl' => 'test.jpg',
    'Region' => 'ap-shanghai', // 用户覆盖
];
// 最终请求参数会包含用户指定的 Region

// 3. 如果用户未指定 Region，使用服务默认值
$params = ['ImageUrl' => 'test.jpg'];
// 最终请求参数会自动添加 Region: 'ap-guangzhou'
```

### 工厂模式实现

**Decision**: 使用静态工厂方法创建客户端实例

**Rationale**:
- 提供简洁的 API 接口
- 支持可选的 access_key 参数
- 便于依赖注入和测试

**Alternatives considered**:
- 构造函数注入：使用复杂
- 服务定位器：违反依赖注入原则

**Implementation approach**:
```php
class QCloudFactory
{
    public static function make(string $product, ?string $accessKey = null): ClientInterface
    {
        $config = app(ConfigInterface::class);
        $logger = app(LoggerInterface::class);
        
        return match($product) {
            'ocr' => new OcrClient($config, $logger, $accessKey),
            'faceid' => new FaceIdClient($config, $logger, $accessKey),
            'captcha' => new CaptchaClient($config, $logger, $accessKey),
            default => throw new InvalidArgumentException("Unsupported product: {$product}"),
        };
    }
}
```

## 最佳实践研究

### Laravel Package 开发最佳实践

**Decision**: 遵循 Laravel Package 开发标准

**Rationale**:
- 确保与 Laravel 生态系统的兼容性
- 提供标准的安装和配置流程
- 支持 Laravel 的自动发现功能

**Implementation approach**:
```php
// ServiceProvider
class QCloudServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ConfigInterface::class, Config::class);
        $this->app->singleton(LoggerInterface::class, Logger::class);
    }
    
    public function boot(): void
    {
        $this->publishes([
            __DIR__.'/../config/easy-qcloud.php' => config_path('easy-qcloud.php'),
        ], 'config');
    }
}
```

### 测试策略

**Decision**: 采用分层测试策略，覆盖单元、集成和功能测试

**Rationale**:
- 确保代码质量和可靠性
- 支持持续集成和部署
- 便于回归测试

**Implementation approach**:
```php
// 单元测试
class SignatureTest extends TestCase
{
    public function test_can_generate_valid_signature(): void
    {
        $signature = new Signature();
        $result = $signature->generate($params);
        $this->assertIsString($result);
    }
}

// 集成测试
class ApiIntegrationTest extends TestCase
{
    public function test_can_call_ocr_api(): void
    {
        $client = QCloudFactory::make('ocr');
        $response = $client->idCardOcr(['ImageUrl' => 'test.jpg']);
        $this->assertArrayHasKey('Response', $response);
    }
}
```

## 技术约束和限制

### PHP 版本要求
- 最低 PHP 8.4+（利用最新语言特性）
- 启用严格类型声明

### Laravel 兼容性
- 支持 Laravel 8+ 版本
- 使用 Laravel 的服务容器和依赖注入

### 性能考虑
- 不定义具体性能目标
- 关注功能实现而非性能优化
- 避免不必要的依赖和复杂度

### 安全要求
- 敏感信息通过配置传入
- 日志中脱敏处理
- 支持环境变量配置

## 总结

通过研究确定了技术选型和实现策略，所有决策都基于 Laravel 生态系统的最佳实践，确保包的易用性、可维护性和扩展性。下一步将进行详细的数据模型设计和 API 契约定义。
