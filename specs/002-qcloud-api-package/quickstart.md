# Quick Start: QCloud API Laravel Package

**Date**: 2024-12-19  
**Feature**: QCloud API Laravel Package  
**Purpose**: 提供快速开始指南，帮助开发者快速集成和使用包

## 安装

### 通过 Composer 安装

```bash
composer require lonquan/easy-qcloud-apis
```

### 发布配置文件

```bash
php artisan vendor:publish --provider="EasyQCloudApi\ServiceProvider" --tag="config"
```

## 配置

### 1. 环境变量配置

在 `.env` 文件中添加腾讯云认证信息：

```env
# 默认配置
QCLOUD_SECRET_ID=your_secret_id
QCLOUD_SECRET_KEY=your_secret_key

# 多环境配置
QCLOUD_PRODUCTION_SECRET_ID=prod_secret_id
QCLOUD_PRODUCTION_SECRET_KEY=prod_secret_key

QCLOUD_TESTING_SECRET_ID=test_secret_id
QCLOUD_TESTING_SECRET_KEY=test_secret_key
```

### 2. 配置文件设置

编辑 `config/easy-qcloud.php`：

```php
<?php

return [
    'default' => 'production',
    
    'profiles' => [
        'production' => [
            'secret_id' => env('QCLOUD_SECRET_ID'),
            'secret_key' => env('QCLOUD_SECRET_KEY'),
            'language' => 'zh-CN',
        ],
        'testing' => [
            'secret_id' => env('QCLOUD_TESTING_SECRET_ID'),
            'secret_key' => env('QCLOUD_TESTING_SECRET_KEY'),
            'language' => 'zh-CN',
        ],
    ],
    
    'services' => [
        'ocr' => [
            'domain' => 'ocr.tencentcloudapi.com',
            'version' => '2018-11-19',
            'region' => 'ap-guangzhou',
            'enabled' => true,
        ],
        'faceid' => [
            'domain' => 'faceid.tencentcloudapi.com',
            'version' => '2018-03-01',
            'region' => 'ap-beijing',
            'enabled' => true,
        ],
        'captcha' => [
            'domain' => 'captcha.tencentcloudapi.com',
            'version' => '2019-07-22',
            'region' => 'ap-shanghai',
            'enabled' => true,
        ],
    ],
];
```

## 基本使用

### 1. 创建客户端

```php
use EasyQCloudApi\QCloudFactory;

// 使用默认 access_key
$ocrClient = QCloudFactory::make('ocr');

// 使用指定 access_key
$ocrClient = QCloudFactory::make('ocr', 'production');
```

### 2. OCR 身份证识别

```php
// 快速方法 - 使用服务默认 region
$response = $ocrClient->idCardOcr([
    'ImageUrl' => 'https://example.com/id-card.jpg',
    'CardSide' => 'FRONT',
    'Config' => [
        'CropIdCard' => true,
        'CropPortrait' => true,
    ],
]);

// 快速方法 - 覆盖 region
$response = $ocrClient->idCardOcr([
    'ImageUrl' => 'https://example.com/id-card.jpg',
    'CardSide' => 'FRONT',
    'Region' => 'ap-shanghai', // 覆盖默认 region
]);

// 通用方法
$response = $ocrClient->request([
    'Action' => 'IDCardOCR',
    'Version' => '2018-11-19',
    'ImageUrl' => 'https://example.com/id-card.jpg',
    'CardSide' => 'FRONT',
    'Region' => 'ap-beijing', // 可选的 region 覆盖
]);
```

### 3. 银行卡识别

```php
$response = $ocrClient->bankCardOcr([
    'ImageUrl' => 'https://example.com/bank-card.jpg',
]);
```

### 4. 营业执照识别

```php
$response = $ocrClient->bizLicenseOcr([
    'ImageUrl' => 'https://example.com/business-license.jpg',
]);
```

## 高级使用

### 1. Region 配置说明

Region 配置在 `services` 中，每个服务可以配置不同的默认地域：

```php
'services' => [
    'ocr' => [
        'region' => 'ap-guangzhou', // OCR 默认使用广州地域
    ],
    'faceid' => [
        'region' => 'ap-beijing',   // FaceID 默认使用北京地域
    ],
],
```

**Region 参数优先级**:
1. 用户输入参数中的 `Region`（最高优先级）
2. 服务配置中的 `region`（默认值）
3. 如果都未配置，则不传递 Region 参数

**使用示例**:
```php
// 使用服务默认 region (ap-guangzhou)
$response = $ocrClient->idCardOcr(['ImageUrl' => 'test.jpg']);

// 覆盖 region
$response = $ocrClient->idCardOcr([
    'ImageUrl' => 'test.jpg',
    'Region' => 'ap-shanghai', // 使用上海地域
]);
```

### 2. 多环境配置

```php
// 生产环境
$prodClient = QCloudFactory::make('ocr', 'production');

// 测试环境
$testClient = QCloudFactory::make('ocr', 'testing');
```

### 2. FaceID 人脸核身

```php
$faceIdClient = QCloudFactory::make('faceid');

// 获取 SDK Token
$response = $faceIdClient->getFaceIdToken([
    'RuleId' => 'your_rule_id',
    'TerminalId' => 'your_terminal_id',
    'IdCard' => 'your_id_card',
    'Name' => 'your_name',
]);
```

### 3. 实名信息核验

```php
// 身份信息认证
$response = $faceIdClient->idCardVerification([
    'IdCard' => 'your_id_card',
    'Name' => 'your_name',
]);

// 银行卡三要素核验
$response = $faceIdClient->bankCardVerification([
    'IdCard' => 'your_id_card',
    'Name' => 'your_name',
    'BankCard' => 'your_bank_card',
]);
```

## 错误处理

### 1. 基本错误处理

```php
try {
    $response = $ocrClient->idCardOcr($params);
    
    if (isset($response['Response']['Error'])) {
        // 处理 API 错误
        $error = $response['Response']['Error'];
        Log::error('QCloud API Error', [
            'code' => $error['Code'],
            'message' => $error['Message'],
        ]);
    }
    
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

### 2. 自定义错误处理

```php
use EasyQCloudApi\Exceptions\QCloudException;

try {
    $response = $ocrClient->idCardOcr($params);
} catch (QCloudException $e) {
    // 根据错误类型进行不同处理
    switch (get_class($e)) {
        case ConfigException::class:
            // 配置错误处理
            return response()->json(['error' => '配置错误'], 500);
            
        case RequestException::class:
            // 请求错误处理
            return response()->json(['error' => '请求失败'], 400);
            
        default:
            // 其他错误处理
            return response()->json(['error' => '服务异常'], 500);
    }
}
```

## 日志记录

### 1. 查看日志

包会自动记录所有 API 调用到 Laravel 日志系统：

```php
// 在 config/logging.php 中配置 QCloud 日志频道
'channels' => [
    'qcloud' => [
        'driver' => 'daily',
        'path' => storage_path('logs/qcloud.log'),
        'level' => 'info',
        'days' => 14,
    ],
],
```

### 2. 日志内容

日志包含以下信息：
- 服务名称 (ocr, faceid, captcha)
- 请求参数
- 响应结果
- 时间戳
- access_key 标识（脱敏）

## 测试

### 1. 单元测试

```php
use EasyQCloudApi\QCloudFactory;
use EasyQCloudApi\Exceptions\ConfigException;

class QCloudTest extends TestCase
{
    public function test_can_create_ocr_client(): void
    {
        $client = QCloudFactory::make('ocr');
        $this->assertInstanceOf(OcrClient::class, $client);
    }
    
    public function test_can_call_id_card_ocr(): void
    {
        $client = QCloudFactory::make('ocr');
        
        $response = $client->idCardOcr([
            'ImageUrl' => 'https://example.com/test.jpg',
        ]);
        
        $this->assertArrayHasKey('Response', $response);
    }
}
```

### 2. 集成测试

```php
public function test_can_integrate_with_laravel(): void
    {
        // 测试 Laravel 服务容器集成
        $client = app('qcloud.ocr');
        $this->assertInstanceOf(OcrClient::class, $client);
        
        // 测试配置加载
        $config = app('qcloud.config');
        $this->assertArrayHasKey('profiles', $config->all());
    }
```

## 技术实现

### 1. V3 签名算法

包使用腾讯云官方 TC3-HMAC-SHA256 签名算法，基于已验证的参考实现：

```php
// 签名算法特点
- 使用 TC3-HMAC-SHA256 算法
- 支持多地域配置
- 自动处理时间戳和凭证范围
- 符合腾讯云官方规范
```

**签名流程**:
1. 构建规范请求字符串 (Canonical Request)
2. 构建待签名字符串 (String to Sign)
3. 计算签名 (Signature)
4. 构建 Authorization 头部

### 2. 自动参数合并

```php
// 服务配置
'services' => [
    'ocr' => [
        'region' => 'ap-guangzhou',
        'version' => '2018-11-19',
    ],
],

// 用户调用
$response = $ocrClient->idCardOcr([
    'ImageUrl' => 'test.jpg',
    'Region' => 'ap-shanghai', // 覆盖默认 region
]);

// 最终请求参数
[
    'Action' => 'IDCardOCR',
    'Version' => '2018-11-19',  // 来自服务配置
    'Region' => 'ap-shanghai',  // 用户覆盖
    'ImageUrl' => 'test.jpg',   // 用户参数
]
```

## 最佳实践

### 1. 配置管理

- 使用环境变量存储敏感信息
- 为不同环境配置不同的 profile
- 定期轮换访问密钥

### 2. 错误处理

- 始终使用 try-catch 处理异常
- 记录详细的错误日志
- 提供用户友好的错误信息

### 3. 性能优化

- 合理使用缓存
- 避免频繁创建客户端实例
- 使用连接池（如适用）

### 4. 安全考虑

- 不要在代码中硬编码密钥
- 使用 HTTPS 传输图片
- 定期更新依赖包

## 故障排除

### 1. 常见问题

**问题**: 配置错误
```
ConfigException: Missing profile 'production'
```
**解决**: 检查配置文件中的 profile 配置

**问题**: 签名错误
```
RequestException: Signature verification failed
```
**解决**: 检查 secret_id 和 secret_key 是否正确

**问题**: 网络超时
```
RequestException: Network timeout
```
**解决**: 检查网络连接和腾讯云服务状态

### 2. 调试技巧

- 启用详细日志记录
- 检查 Laravel 日志文件
- 使用腾讯云控制台验证配置

### 3. 获取帮助

- 查看腾讯云官方文档
- 检查包的 GitHub 仓库
- 提交 Issue 或 Pull Request

## 更新日志

### v0.1.0 (计划中)
- 初始版本发布
- 支持 OCR 和 FaceID 产品
- 基本配置和错误处理

### 未来计划
- 支持更多腾讯云产品
- 添加缓存功能
- 性能优化
- 更多测试覆盖
