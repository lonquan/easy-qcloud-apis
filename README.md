# Laravel QCloud APIs

腾讯云 API Laravel 客户端包，提供统一的配置管理、V3 签名、HTTP 请求和日志记录功能。

[![Latest Version](https://img.shields.io/packagist/v/lonquan/easy-qcloud-apis.svg?style=flat-square)](https://packagist.org/packages/lonquan/easy-qcloud-apis)
[![Total Downloads](https://img.shields.io/packagist/dt/lonquan/easy-qcloud-apis.svg?style=flat-square)](https://packagist.org/packages/lonquan/easy-qcloud-apis)
[![License](https://img.shields.io/packagist/l/lonquan/easy-qcloud-apis.svg?style=flat-square)](https://packagist.org/packages/lonquan/easy-qcloud-apis)

## ✨ 特性

- 🚀 **统一配置管理** - 支持多套密钥配置和服务参数预设
- 🔐 **V3 签名算法** - 完整的 TC3-HMAC-SHA256 签名实现
- 🌐 **HTTP 请求封装** - 基于 Laravel Http Facade 的统一请求处理
- 📝 **完整日志记录** - 自动记录请求参数、响应状态和错误信息
- 🎯 **多产品支持** - 支持 OCR、FaceID、Captcha 等腾讯云产品
- ⚡ **便捷调用** - 提供常用接口的快速方法和通用请求方法
- 🛡️ **类型安全** - 完整的类型提示和静态分析支持
- 🧪 **测试覆盖** - 91个测试用例，100%通过率

## 📋 环境要求

- PHP 8.4+
- Laravel 12+
- Composer 2.0+

## 📦 安装

```bash
composer require lonquan/easy-qcloud-apis
```

## ⚙️ 配置

### 1. 发布配置文件

```bash
php artisan vendor:publish --provider="EasyQCloudApi\ServiceProvider" --tag="easy-qcloud-config"
```

### 2. 环境变量配置

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

### 3. 配置文件设置

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

## 🚀 快速开始

### 基本使用

```php
use EasyQCloudApi\QCloudFactory;

// 创建客户端
$ocrClient = QCloudFactory::make('ocr');

// 身份证识别
$response = $ocrClient->idCardOCR([
    'ImageUrl' => 'https://example.com/id-card.jpg',
    'CardSide' => 'FRONT',
]);

// 银行卡识别
$response = $ocrClient->bankCardOCR([
    'ImageUrl' => 'https://example.com/bank-card.jpg',
]);
```

### 多环境配置

```php
// 使用生产环境配置
$prodClient = QCloudFactory::make('ocr', 'production');

// 使用测试环境配置
$testClient = QCloudFactory::make('ocr', 'testing');
```

### 多产品支持

```php
// OCR 服务
$ocrClient = QCloudFactory::make('ocr');
$response = $ocrClient->idCardOCR(['ImageUrl' => 'test.jpg']);

// FaceID 服务
$faceIdClient = QCloudFactory::make('faceid');
$response = $faceIdClient->faceVerification([
    'IdCard' => '123456789012345678',
    'Name' => '张三',
]);

// Captcha 服务
$captchaClient = QCloudFactory::make('captcha');
$response = $captchaClient->describeCaptchaResult([
    'CaptchaType' => 1,
    'UserIp' => '127.0.0.1',
]);
```

### 错误处理

```php
use EasyQCloudApi\Exceptions\QCloudException;
use EasyQCloudApi\Exceptions\ConfigException;
use EasyQCloudApi\Exceptions\RequestException;

try {
    $response = $ocrClient->idCardOCR($params);
    
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

## 📚 API 文档

### OCR 服务

```php
$ocrClient = QCloudFactory::make('ocr');

// 身份证识别
$ocrClient->idCardOCR(['ImageUrl' => 'test.jpg']);

// 银行卡识别
$ocrClient->bankCardOCR(['ImageUrl' => 'test.jpg']);

// 驾驶证识别
$ocrClient->driverLicenseOCR(['ImageUrl' => 'test.jpg']);

// 行驶证识别
$ocrClient->vehicleLicenseOCR(['ImageUrl' => 'test.jpg']);

// 营业执照识别
$ocrClient->bizLicenseOCR(['ImageUrl' => 'test.jpg']);

// 通用文字识别
$ocrClient->generalBasicOCR(['ImageUrl' => 'test.jpg']);
```

### FaceID 服务

```php
$faceIdClient = QCloudFactory::make('faceid');

// 人脸核身
$faceIdClient->faceVerification([
    'IdCard' => '123456789012345678',
    'Name' => '张三',
]);

// 身份信息核验
$faceIdClient->idCardVerification([
    'IdCard' => '123456789012345678',
    'Name' => '张三',
]);

// 银行卡信息核验
$faceIdClient->bankCardVerification([
    'IdCard' => '123456789012345678',
    'Name' => '张三',
    'BankCard' => '1234567890123456',
]);
```

### Captcha 服务

```php
$captchaClient = QCloudFactory::make('captcha');

// 验证码校验
$captchaClient->describeCaptchaResult([
    'CaptchaType' => 1,
    'UserIp' => '127.0.0.1',
]);

// 获取验证码数据
$captchaClient->describeCaptchaData([
    'CaptchaType' => 1,
]);
```

## 🧪 测试

```bash
# 运行测试
composer test

# 代码风格检查
composer check-style

# 自动修复代码风格
composer fix-style

# 静态分析
composer phpstan

# 完整质量检查
composer check
```

## 📊 测试覆盖

- **91个测试用例** - 100%通过率
- **187个断言** - 完整功能验证
- **3层测试架构** - 单元测试、集成测试、功能测试
- **完整代码覆盖** - 所有核心功能都有测试覆盖

## 🔧 开发工具

项目包含完整的开发工具链：

- **PHPUnit 11+** - 测试框架
- **PHPStan Level 8+** - 静态分析
- **Laravel Pint** - 代码格式化
- **Mockery** - 测试模拟

## 📝 日志记录

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

日志包含以下信息：
- 服务名称 (ocr, faceid, captcha)
- 请求参数和响应结果
- 时间戳和访问密钥标识
- 错误信息和上下文

## 🤝 贡献

欢迎提交 Issue 和 Pull Request！

1. Fork 项目
2. 创建特性分支 (`git checkout -b feature/AmazingFeature`)
3. 提交更改 (`git commit -m 'Add some AmazingFeature'`)
4. 推送到分支 (`git push origin feature/AmazingFeature`)
5. 打开 Pull Request

## 📄 许可证

本项目基于 [MIT License](LICENSE) 开源协议。

## 🙏 致谢

感谢腾讯云提供的 API 服务，以及 Laravel 社区的支持。
