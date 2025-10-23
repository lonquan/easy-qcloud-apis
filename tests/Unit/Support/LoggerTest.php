<?php

declare(strict_types=1);

namespace EasyQCloudApi\Tests\Unit\Support;

use EasyQCloudApi\Support\Logger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface as PsrLoggerInterface;

/**
 * Logger 测试类
 */
class LoggerTest extends TestCase
{
    private Logger $logger;

    private PsrLoggerInterface $psrLogger;

    protected function setUp(): void
    {
        $this->psrLogger = $this->createMock(PsrLoggerInterface::class);
        $this->logger = new Logger($this->psrLogger);
    }

    public function test_log_request(): void
    {
        $this->psrLogger->expects($this->once())
            ->method('info')
            ->with(
                'QCloud API Request',
                $this->callback(function ($context) {
                    return $context['service'] === 'ocr' &&
                           $context['action'] === 'IdCardOCR' &&
                           $context['params'] === ['ImageUrl' => 'test.jpg'] &&
                           $context['access_key'] === 'default' &&
                           isset($context['timestamp']);
                })
            );

        $this->logger->logRequest('ocr', 'IdCardOCR', ['ImageUrl' => 'test.jpg'], 'default');
    }

    public function test_log_response(): void
    {
        $this->psrLogger->expects($this->once())
            ->method('info')
            ->with(
                'QCloud API Response',
                $this->callback(function ($context) {
                    return $context['service'] === 'ocr' &&
                           $context['action'] === 'IdCardOCR' &&
                           $context['response'] === ['Response' => ['RequestId' => 'test']] &&
                           $context['status_code'] === 200 &&
                           $context['access_key'] === 'default' &&
                           isset($context['timestamp']);
                })
            );

        $this->logger->logResponse('ocr', 'IdCardOCR', ['Response' => ['RequestId' => 'test']], 200, 'default');
    }

    public function test_log_error(): void
    {
        $exception = new \Exception('Test error');

        $this->psrLogger->expects($this->once())
            ->method('error')
            ->with(
                'QCloud API Error',
                $this->callback(function ($context) {
                    return $context['service'] === 'ocr' &&
                           $context['action'] === 'IdCardOCR' &&
                           $context['message'] === 'Test error' &&
                           $context['access_key'] === 'default' &&
                           isset($context['timestamp']);
                })
            );

        $this->logger->logError('ocr', 'IdCardOCR', 'Test error', ['exception' => $exception], 'default');
    }

    public function test_logger_without_psr_logger(): void
    {
        $logger = new Logger(null);

        // 应该不抛出异常
        $this->expectNotToPerformAssertions();
        $logger->logRequest('ocr', 'IdCardOCR', ['ImageUrl' => 'test.jpg'], 'default');
        $logger->logResponse('ocr', 'IdCardOCR', ['Response' => ['RequestId' => 'test']], 200, 'default');
        $logger->logError('ocr', 'IdCardOCR', 'Test error', [], 'default');
    }

    public function test_log_request_with_null_access_key(): void
    {
        $this->psrLogger->expects($this->once())
            ->method('info')
            ->with(
                'QCloud API Request',
                $this->callback(function ($context) {
                    return $context['service'] === 'faceid' &&
                           $context['action'] === 'FaceVerification' &&
                           $context['params'] === ['IdCard' => '123456789012345678'] &&
                           $context['access_key'] === null &&
                           isset($context['timestamp']);
                })
            );

        $this->logger->logRequest('faceid', 'FaceVerification', ['IdCard' => '123456789012345678'], null);
    }

    public function test_log_response_with_different_status_codes(): void
    {
        $this->psrLogger->expects($this->exactly(3))
            ->method('info')
            ->with(
                'QCloud API Response',
                $this->callback(function ($context) {
                    return $context['service'] === 'captcha' &&
                           $context['action'] === 'DescribeCaptchaResult' &&
                           isset($context['status_code']) &&
                           in_array($context['status_code'], [200, 400, 500]) &&
                           isset($context['timestamp']);
                })
            );

        $this->logger->logResponse('captcha', 'DescribeCaptchaResult', ['Response' => ['RequestId' => 'test']], 200, 'default');
        $this->logger->logResponse('captcha', 'DescribeCaptchaResult', ['Error' => ['Code' => 'InvalidParameter']], 400, 'default');
        $this->logger->logResponse('captcha', 'DescribeCaptchaResult', ['Error' => ['Code' => 'InternalError']], 500, 'default');
    }

    public function test_log_error_with_empty_context(): void
    {
        $this->psrLogger->expects($this->once())
            ->method('error')
            ->with(
                'QCloud API Error',
                $this->callback(function ($context) {
                    return $context['service'] === 'ocr' &&
                           $context['action'] === 'IdCardOCR' &&
                           $context['message'] === 'Network timeout' &&
                           $context['context'] === [] &&
                           $context['access_key'] === 'production' &&
                           isset($context['timestamp']);
                })
            );

        $this->logger->logError('ocr', 'IdCardOCR', 'Network timeout', [], 'production');
    }

    public function test_log_error_with_complex_context(): void
    {
        $exception = new \Exception('Database connection failed');
        $context = [
            'exception' => $exception,
            'request_id' => 'req_123456',
            'user_id' => 12345,
            'additional_data' => ['key' => 'value'],
        ];

        $this->psrLogger->expects($this->once())
            ->method('error')
            ->with(
                'QCloud API Error',
                $this->callback(function ($context) {
                    return $context['service'] === 'faceid' &&
                           $context['action'] === 'IdCardVerification' &&
                           $context['message'] === 'Database connection failed' &&
                           is_array($context['context']) &&
                           isset($context['context']['exception']) &&
                           isset($context['context']['request_id']) &&
                           $context['access_key'] === 'testing' &&
                           isset($context['timestamp']);
                })
            );

        $this->logger->logError('faceid', 'IdCardVerification', 'Database connection failed', $context, 'testing');
    }

    public function test_log_request_with_empty_params(): void
    {
        $this->psrLogger->expects($this->once())
            ->method('info')
            ->with(
                'QCloud API Request',
                $this->callback(function ($context) {
                    return $context['service'] === 'captcha' &&
                           $context['action'] === 'DescribeCaptchaData' &&
                           $context['params'] === [] &&
                           $context['access_key'] === 'default' &&
                           isset($context['timestamp']);
                })
            );

        $this->logger->logRequest('captcha', 'DescribeCaptchaData', [], 'default');
    }

    public function test_log_response_with_empty_response(): void
    {
        $this->psrLogger->expects($this->once())
            ->method('info')
            ->with(
                'QCloud API Response',
                $this->callback(function ($context) {
                    return $context['service'] === 'ocr' &&
                           $context['action'] === 'GeneralBasicOCR' &&
                           $context['response'] === [] &&
                           $context['status_code'] === 200 &&
                           $context['access_key'] === null &&
                           isset($context['timestamp']);
                })
            );

        $this->logger->logResponse('ocr', 'GeneralBasicOCR', [], 200, null);
    }

    public function test_constructor_with_psr_logger(): void
    {
        $psrLogger = $this->createMock(PsrLoggerInterface::class);
        $logger = new Logger($psrLogger);

        $this->assertInstanceOf(Logger::class, $logger);
    }

    public function test_constructor_without_psr_logger(): void
    {
        $logger = new Logger(null);

        $this->assertInstanceOf(Logger::class, $logger);
    }
}
