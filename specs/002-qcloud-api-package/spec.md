# Feature Specification: QCloud API Laravel Package

**Feature Branch**: `002-qcloud-api-package`  
**Created**: 2024-12-19  
**Status**: Draft  
**Input**: User description: "我需要从零开始 构建一个 QCloud APIs For Laravel 的 composer package, 这个 package 只为 laravel 设计 需要有如下的功能

1. 统一的配置管理模块 Config， 配置分为两部分内容, @config/easy-qcloud.php 有配置示例
   - profiles:可托管多对 secret_id 和 secret_key
   - services:可预设各个产品接口的输入参数, 可参考 @config/qcloud.php
2. 统一的 V3 签名模块
3. 统一的 HTTP 请求模块, 使用 laravel Http Facade
4. 实现日志记录: 请求参数, 响应情况的日志
5. 多个产品的请求封装, 如 ocr 产品, faceid 产品, captcha 产品等
6. 封装只需关键产品接口, 不关心接口的参数, 参数由用户控制
7. 只关心接口是否调用成功, 不关心具体也是是否完成, 交由用户处理"

## User Scenarios & Testing *(mandatory)*

### User Story 1 - 配置管理 (Priority: P1)

Laravel 开发者需要能够通过配置文件管理多个腾讯云账户的认证信息和产品服务配置，以便在不同环境中使用不同的认证凭据。

**Why this priority**: 这是整个 package 的基础，没有配置管理就无法进行任何 API 调用。

**Independent Test**: 可以通过验证配置文件加载和多个 profile 切换来独立测试，确保开发者能够正确配置和使用不同的认证凭据。

**Acceptance Scenarios**:

1. **Given** 开发者安装了 package，**When** 发布配置文件，**Then** 系统应该提供默认的配置模板
2. **Given** 开发者配置了多个 profile，**When** 切换不同的 profile，**Then** 系统应该使用对应的认证凭据
3. **Given** 开发者配置了服务参数，**When** 调用 API，**Then** 系统应该使用预设的服务配置

---

### User Story 2 - API 调用 (Priority: P1)

Laravel 开发者需要能够通过简单的工厂方法获取不同产品的客户端，并使用统一的接口调用腾讯云 API。

**Why this priority**: 这是 package 的核心功能，用户的主要使用场景。

**Independent Test**: 可以通过创建客户端实例并调用 API 来独立测试，确保开发者能够成功调用腾讯云服务。

**Acceptance Scenarios**:

1. **Given** 开发者配置了认证信息，**When** 通过工厂方法创建 OCR 客户端（不指定 access_key），**Then** 系统应该使用默认的 access_key 返回可用的客户端实例
2. **Given** 开发者配置了多个 access_key，**When** 通过工厂方法创建客户端并指定 access_key，**Then** 系统应该使用指定的 access_key 创建客户端实例
3. **Given** 开发者有客户端实例，**When** 调用通用 request 方法（不指定 access_key），**Then** 系统应该使用客户端的默认 access_key 发送请求
4. **Given** 开发者有客户端实例，**When** 调用通用 request 方法并指定 access_key，**Then** 系统应该使用指定的 access_key 发送请求
5. **Given** 开发者有客户端实例，**When** 调用快速方法（如 idCardOCR）并指定 access_key，**Then** 系统应该使用指定的 access_key 发送请求

---

### User Story 3 - 日志记录 (Priority: P2)

Laravel 开发者需要能够查看 API 调用的详细日志，包括请求参数和响应结果，以便调试和监控。

**Why this priority**: 虽然不是核心功能，但对开发和运维很重要，有助于问题排查。

**Independent Test**: 可以通过检查日志文件或日志输出来独立测试，确保所有 API 调用都被正确记录。

**Acceptance Scenarios**:

1. **Given** 开发者启用了日志功能，**When** 调用 API，**Then** 系统应该记录请求参数和响应结果
2. **Given** 开发者配置了日志级别，**When** 发生错误，**Then** 系统应该记录详细的错误信息

---

### User Story 4 - 多产品支持 (Priority: P2)

Laravel 开发者需要能够使用同一个 package 调用多个腾讯云产品（OCR、FaceID、Captcha 等）的 API。

**Why this priority**: 扩展了 package 的适用范围，提供一站式解决方案。

**Independent Test**: 可以通过创建不同产品的客户端来独立测试，确保每个产品都有对应的封装。

**Acceptance Scenarios**:

1. **Given** 开发者需要 OCR 服务，**When** 创建 OCR 客户端，**Then** 系统应该提供 OCR 相关的 API 方法
2. **Given** 开发者需要 FaceID 服务，**When** 创建 FaceID 客户端，**Then** 系统应该提供 FaceID 相关的 API 方法

---

### Edge Cases

- 当认证凭据无效时，系统如何处理签名失败？
- 当网络超时时，系统如何处理重试？
- 当 API 返回错误时，系统如何提供有意义的错误信息？
- 当配置文件格式错误时，系统如何提供清晰的错误提示？

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: Package MUST 提供统一的配置管理，支持多个 profile 和预设服务参数，不提供默认值，要求用户必须配置所有参数
- **FR-002**: Package MUST 验证必需配置（secret_id, secret_key），当配置缺失或无效时抛出清晰的异常
- **FR-003**: Package MUST 实现腾讯云 V3 签名算法（TC3-HMAC-SHA256）
- **FR-004**: Package MUST 使用 Laravel Http Facade 进行 HTTP 请求
- **FR-005**: Package MUST 记录所有 API 调用到 Laravel 日志系统，包含请求服务、请求参数、响应结果、时间戳、access_key 标识
- **FR-006**: Package MUST 提供工厂方法创建不同产品的客户端，支持可选的 access_key 参数
- **FR-007**: Package MUST 支持通用 request 方法和产品特定的快速方法，所有方法都支持可选的 access_key 参数
- **FR-008**: Package MUST 为 requires.md 中提及的所有接口提供快速方法，包括：
  - **OCR 接口** (20个): GeneralBasicOCR, GeneralAccurateOCR, IDCardOCR, BankCardOCR, VehicleLicenseOCR, DriverLicenseOCR, BizLicenseOCR, BusinessCardOCR, VehicleRegCertOCR, MainlandPermitOCR, HmtResidentPermitOCR, HKIDCardOCR, MLIDPassportOCR, RecognizeForeignPermanentResidentIdCard, RecognizeEncryptedIDCardOCR, RecognizeValidIDCardOCR, RecognizeGeneralCardWarn, RecognizeGeneralTextImageWarn, RecognizeTableAccurateOCR, ClassifyStoreName, RecognizeStoreName, ClassifyDetectOCR
  - **FaceID 接口** (20个): CheckEidTokenStatus, DetectAuth, GetDetectInfo, GetDetectInfoEnhanced, GetEidResult, GetEidToken, GetFaceIdResult, GetFaceIdToken, IdCardVerification, IdCardOCRVerification, CheckIdNameDate, CheckBankCardInformation, BankCard2EVerification, BankCardVerification, BankCard4EVerification, MobileNetworkTimeVerification, MobileStatus, PhoneVerification, PhoneVerificationCMCC, PhoneVerificationCTCC, PhoneVerificationCUCC, CheckPhoneAndName, MinorsVerification, EncryptedPhoneVerification
  - **Captcha 接口**: 根据腾讯云官方文档实现
- **FR-009**: Package MUST 只关心 API 调用成功与否，不处理具体业务逻辑
- **FR-010**: Package MUST 支持 OCR、FaceID、Captcha 等主要产品
- **FR-011**: Package MUST 提供基本的错误处理（仅 API 调用层面，判断请求是否成功，不处理响应内容判断）
- **FR-012**: Package MUST 支持自定义域名和地域配置
- **FR-013**: Package MUST 在未指定 access_key 时使用默认的 access_key，在指定 access_key 时使用指定的凭据

### Key Entities

- **Profile**: 代表一组认证凭据（secret_id, secret_key），用于标识不同的腾讯云账户
- **Service**: 代表一个腾讯云产品服务，包含域名、版本等配置信息
- **Client**: 代表一个产品客户端，封装了该产品的 API 调用方法
- **Request**: 代表一次 API 调用，包含请求参数、签名信息和响应结果

## Clarifications

### Session 2024-12-19

- Q: 当 API 调用失败时（网络超时、认证失败、腾讯云 API 错误），package 应该如何响应？是否需要自动重试机制？ → A: 只提供基本的错误处理（仅 API 调用层面的，比如是否请求成功，不对响应判断），不包含自动重试
- Q: 日志记录应该包含哪些信息？应该存储在哪里？是否需要支持不同的日志级别？ → A: 记录到 Laravel 日志系统，包含请求服务(ocr/faceid)、请求参数、响应结果、时间戳、access_key 标识
- Q: 配置文件应该如何处理默认值？是否需要验证必需配置（如 secret_id, secret_key）？当配置缺失或无效时应该如何响应？ → A: 不提供默认值，要求用户必须配置所有参数，secret_id secret_key 需要校验
- Q: 应该为哪些腾讯云 API 接口提供快速方法？是只针对最常用的接口，还是覆盖每个产品的主要接口？ → A: 为 requires.md 中提及的所有接口都提供快速方法
- Q: Package 是否需要定义具体的性能目标（如最大并发数、响应时间限制）？是否需要考虑监控、告警等运维相关的质量属性？ → A: 不定义具体的性能目标，只关注功能实现

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: 开发者能够在 5 分钟内完成 package 的安装和基本配置
- **SC-002**: 开发者能够通过简单的工厂方法在 30 秒内创建可用的客户端，支持可选的 access_key 参数
- **SC-003**: API 调用成功率应该达到 99% 以上（排除网络和认证问题）
- **SC-004**: 配置验证应该提供清晰的错误信息，帮助开发者快速定位配置问题
- **SC-005**: 日志记录应该包含完整的请求和响应信息，便于问题排查
- **SC-006**: Package 应该支持至少 3 个主要腾讯云产品（OCR、FaceID、Captcha）
- **SC-007**: 错误信息应该清晰明确，帮助开发者快速定位问题
- **SC-008**: Package 应该与 Laravel 8+ 版本兼容，不影响现有应用性能