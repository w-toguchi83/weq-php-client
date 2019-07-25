<?php

namespace Tests\Unit\Util;

use Tests\TestCase;
use WeqClient\Config;
use WeqClient\Util\ConfigUtil;

class ConfigUtilTest extends TestCase
{
    /**
     * @dataProvider makeBaseUrlProvider
     */
    public function testMakeBaseUrl($expected, $config)
    {
        $this->assertEquals($expected, ConfigUtil::makeBaseUrl($config));
    }

    public function makeBaseUrlProvider()
    {
        return [
            ['https://sample.weq.com', Config::create('sample.weq.com')],
            ['http://sample.weq.com', Config::create('sample.weq.com')->setSecure(false)],
        ];
    }
}
