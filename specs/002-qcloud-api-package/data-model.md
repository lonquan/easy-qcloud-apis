# Data Model: QCloud API Laravel Package

**Date**: 2024-12-19  
**Feature**: QCloud API Laravel Package  
**Purpose**: 定义包的核心数据模型和实体关系

## 核心实体

### Profile (认证配置)

**描述**: 代表一组腾讯云认证凭据，用于标识不同的账户

**属性**:
- `name: string` - Profile 名称 (主键)
- `secret_id: string` - 腾讯云 Secret ID
- `secret_key: string` - 腾讯云 Secret Key
- `language: ?string` - 可选的语言配置 (zh-CN, en-US)

**验证规则**:
- `name` 必须非空且唯一
- `secret_id` 必须非空，长度 16-32 字符
- `secret_key` 必须非空，长度 32-64 字符
- `language` 如果提供，必须是 'zh-CN' 或 'en-US'

**状态转换**:
```
未配置 → 已配置 → 已验证 → 已激活
```

### Service (产品服务配置)

**描述**: 代表一个腾讯云产品服务的配置信息

**属性**:
- `name: string` - 服务名称 (ocr, faceid, captcha)
- `domain: string` - API 域名
- `version: string` - API 版本
- `region: ?string` - 默认地域配置
- `default_params: array` - 默认参数配置
- `enabled: bool` - 是否启用

**验证规则**:
- `name` 必须是支持的产品名称
- `domain` 必须是有效的腾讯云 API 域名
- `version` 必须符合语义化版本格式
- `region` 如果提供，必须是有效的腾讯云地域
- `default_params` 必须是关联数组
- `enabled` 必须是布尔值

**支持的产品**:
- `ocr` - 文字识别服务
- `faceid` - 人脸核身服务  
- `captcha` - 验证码服务

### Request (API 请求)

**描述**: 代表一次腾讯云 API 调用请求

**属性**:
- `id: string` - 请求唯一标识
- `service: string` - 服务名称
- `action: string` - 接口名称
- `version: string` - 接口版本
- `params: array` - 请求参数
- `access_key: ?string` - 使用的认证密钥
- `timestamp: int` - 请求时间戳
- `headers: array` - 请求头
- `signature: string` - 请求签名

**验证规则**:
- `id` 必须唯一
- `service` 必须是支持的服务
- `action` 必须非空
- `version` 必须符合版本格式
- `params` 必须是关联数组
- `timestamp` 必须是有效的 Unix 时间戳
- `signature` 必须是有效的 TC3 签名

### Response (API 响应)

**描述**: 代表腾讯云 API 的响应结果

**属性**:
- `request_id: string` - 请求 ID
- `success: bool` - 是否成功
- `data: array` - 响应数据
- `error: ?array` - 错误信息
- `timestamp: int` - 响应时间戳
- `duration: int` - 请求耗时 (毫秒)

**验证规则**:
- `request_id` 必须非空
- `success` 必须是布尔值
- `data` 在成功时必须有值
- `error` 在失败时必须有值
- `timestamp` 必须是有效时间戳
- `duration` 必须是非负整数

## 实体关系

### Profile ↔ Service (多对多)
- 一个 Profile 可以访问多个 Service
- 一个 Service 可以被多个 Profile 使用
- 通过配置中的 `profiles` 和 `services` 建立关系

### Service ↔ Request (一对多)
- 一个 Service 可以产生多个 Request
- 每个 Request 属于一个 Service
- 通过 `service` 字段建立关系

### Request ↔ Response (一对一)
- 每个 Request 对应一个 Response
- 通过 `request_id` 建立关系

## 数据流

### 配置加载流程
```
配置文件 → Config 类 → Profile 实体 → 验证 → 缓存
```

### API 调用流程
```
Request 参数 → 签名生成 → HTTP 请求 → 响应解析 → Response 实体
```

### 日志记录流程
```
Request 实体 → 日志格式化 → Laravel Logger → 存储/输出
```

## 状态管理

### Profile 状态
- `unconfigured` - 未配置
- `configured` - 已配置
- `validated` - 已验证
- `active` - 已激活
- `invalid` - 无效

### Service 状态
- `disabled` - 已禁用
- `enabled` - 已启用
- `maintenance` - 维护中

### Request 状态
- `pending` - 待发送
- `sent` - 已发送
- `completed` - 已完成
- `failed` - 已失败
- `timeout` - 已超时

## 数据验证

### 输入验证
- 所有用户输入必须经过验证
- 使用 Laravel 验证规则
- 敏感信息必须脱敏处理

### 输出验证
- API 响应必须验证格式
- 错误信息必须标准化
- 日志数据必须脱敏

## 缓存策略

### 配置缓存
- Profile 配置可以缓存
- Service 配置可以缓存
- 缓存键使用服务名称

### 请求缓存
- 不缓存 API 请求
- 不缓存 API 响应
- 确保数据实时性

## 安全考虑

### 敏感信息处理
- `secret_key` 在日志中脱敏
- `access_key` 在日志中脱敏
- 配置文件权限控制

### 数据完整性
- 所有数据必须验证
- 签名必须验证
- 响应必须验证

## 扩展性

### 新产品支持
- 通过配置添加新产品
- 实现对应的客户端类
- 更新工厂方法

### 新参数支持
- 通过配置添加新参数
- 保持向后兼容
- 提供迁移指导
