<?php

declare(strict_types=1);

namespace EasyQCloudApi\Clients;

use EasyQCloudApi\Contracts\ConfigInterface;
use EasyQCloudApi\Contracts\LoggerInterface;
use EasyQCloudApi\Exceptions\QCloudException;
use EasyQCloudApi\Exceptions\RequestException;
use Illuminate\Http\Client\Factory as HttpFactory;

/**
 * OCR客户端
 */
class OcrClient extends BaseClient
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
        return 'ocr';
    }

    /**
     * 通用文字识别
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     *
     * @throws RequestException
     * @throws QCloudException
     */
    public function generalBasicOCR(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('GeneralBasicOCR', $params, $accessKey);
    }

    /**
     * 身份证识别
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     *
     * @throws QCloudException
     * @throws RequestException
     */
    public function idCardOCR(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('IdCardOCR', $params, $accessKey);
    }

    /**
     * 银行卡识别
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     *
     * @throws QCloudException
     * @throws RequestException
     */
    public function bankCardOCR(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('BankCardOCR', $params, $accessKey);
    }

    /**
     * 驾驶证识别
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     *
     * @throws QCloudException
     * @throws RequestException
     */
    public function driverLicenseOCR(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('DriverLicenseOCR', $params, $accessKey);
    }

    /**
     * 行驶证识别
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     *
     * @throws QCloudException
     * @throws RequestException
     */
    public function vehicleLicenseOCR(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('VehicleLicenseOCR', $params, $accessKey);
    }

    /**
     * 营业执照识别
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function bizLicenseOCR(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('BizLicenseOCR', $params, $accessKey);
    }

    /**
     * 护照识别
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function passportOCR(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('PassportOCR', $params, $accessKey);
    }

    /**
     * 港澳台通行证识别
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function permitOCR(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('PermitOCR', $params, $accessKey);
    }

    /**
     * 户口本识别
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function residenceBookletOCR(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('ResidenceBookletOCR', $params, $accessKey);
    }

    /**
     * 房产证识别
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function realEstateOCR(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('RealEstateOCR', $params, $accessKey);
    }

    /**
     * 不动产权证识别
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function propertyOCR(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('PropertyOCR', $params, $accessKey);
    }

    /**
     * 表格识别
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function tableOCR(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('TableOCR', $params, $accessKey);
    }

    /**
     * 通用表格识别
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function generalTableOCR(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('GeneralTableOCR', $params, $accessKey);
    }

    /**
     * 通用票据识别
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function generalInvoiceOCR(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('GeneralInvoiceOCR', $params, $accessKey);
    }

    /**
     * 增值税发票识别
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function vatInvoiceOCR(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('VatInvoiceOCR', $params, $accessKey);
    }

    /**
     * 运单识别
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function waybillOCR(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('WaybillOCR', $params, $accessKey);
    }

    /**
     * 通用机打发票识别
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function generalMachineInvoiceOCR(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('GeneralMachineInvoiceOCR', $params, $accessKey);
    }

    /**
     * 机票行程单识别
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function flightInvoiceOCR(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('FlightInvoiceOCR', $params, $accessKey);
    }

    /**
     * 火车票识别
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function trainTicketOCR(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('TrainTicketOCR', $params, $accessKey);
    }

    /**
     * 出租车发票识别
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function taxiInvoiceOCR(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('TaxiInvoiceOCR', $params, $accessKey);
    }

    /**
     * 定额发票识别
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function quotaInvoiceOCR(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('QuotaInvoiceOCR', $params, $accessKey);
    }

    /**
     * 轮船票识别
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function shipInvoiceOCR(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('ShipInvoiceOCR', $params, $accessKey);
    }

    /**
     * 汽车票识别
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function busInvoiceOCR(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('BusInvoiceOCR', $params, $accessKey);
    }

    /**
     * 过路过桥费发票识别
     *
     * @param  array<string, mixed>  $params  请求参数
     * @param  string|null  $accessKey  访问密钥标识
     * @return array<string, mixed> 响应数据
     */
    public function tollInvoiceOCR(array $params = [], ?string $accessKey = null): array
    {
        return $this->request('TollInvoiceOCR', $params, $accessKey);
    }
}
