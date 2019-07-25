<?php

namespace Tests\Unit;

use Tests\TestCase;
use WeqClient\Joint\ConfigJoint;
use WeqClient\Config;

class ConfigTest extends TestCase
{
    public function testCreate()
    {
        $config = Config::create('sample.weq.com');

        $this->assertTrue($config instanceof ConfigJoint);
        $this->assertTrue($config instanceof Config);

        $this->assertEquals('sample.weq.com', $config->getHost());
        $this->assertEquals(true, $config->isSecure());
        $this->assertEquals(60, $config->getTimeout());
        $this->assertEquals(1, $config->getRetryCount());
        $this->assertEquals(300, $config->getWaitTime());
    }

    public function testCustom()
    {
        $config = Config::create('sample.weq.com');

        $config->setSecure(false)
               ->setHost('custom.weq.com:4000')
               ->setTimeout(300)
               ->setRetryCount(3)
               ->setWaitTime(500);

        $this->assertEquals('custom.weq.com:4000', $config->getHost());
        $this->assertEquals(false, $config->isSecure());
        $this->assertEquals(300, $config->getTimeout());
        $this->assertEquals(3, $config->getRetryCount());
        $this->assertEquals(500, $config->getWaitTime());
    }
}
