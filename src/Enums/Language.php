<?php

declare(strict_types=1);

namespace EasyQCloudApi\Enums;

/**
 * 指定接口返回的语言，仅部分接口支持此参数
 */
enum Language: string
{
    case Chinese = 'zh-CN';
    case English = 'en-US';
}
