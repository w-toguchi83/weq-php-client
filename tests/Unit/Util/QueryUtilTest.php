<?php

namespace Tests\Unit\Util;

use Tests\TestCase;
use WeqClient\Util\QueryUtil;

class QueryUtilTest extends TestCase
{
    public function testFormat()
    {
        $query = "\nSELECT\n*\nFROM tbl\nWHERE id = 100\n";
        $expected = 'SELECT * FROM tbl WHERE id = 100';

        $this->assertEquals($expected, QueryUtil::format($query));
    }
}
