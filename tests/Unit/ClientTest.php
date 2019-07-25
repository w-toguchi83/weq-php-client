<?php

namespace Tests\Unit;

use Tests\TestCase;
use WeqClient\Config;
use WeqClient\Client;

class ClientTest extends TestCase
{
    /**
     * @dataProvider pingProvider
     */
    public function testPing($expected, $client)
    {
        $this->assertEquals($expected, $client->ping());
    }

    public function pingProvider()
    {
        return [
            [true, $this->createWeqClient('weq:4000')],
            [false, $this->createWeqClient('nothing-weq:4000')],
        ];
    }

    public function testFetch()
    {
        $client = $this->createWeqClient('weq:4000');

        $resource = 'weqdb1';
        $query    = 'SELECT title, price FROM m_product WHERE series_id = :series_id';
        $binds = [
            'series_id' => '3000',
        ];

        $rows = [];
        foreach ($client->fetch($resource, $query, $binds) as $r) {
            $this->assertTrue(isset($r['title']) && isset($r['price']));
            $rows[] = $r;
        }

        $this->assertEquals(3, count($rows));
    }

    public function testFetchThrowException()
    {
        $client = $this->createWeqClient('nothing-weq:4000');

        $resource = 'weqdb1';
        $query    = 'SELECT title, price FROM m_product WHERE series_id = :series_id';
        $binds = [
            'series_id' => '3000',
        ];

        $this->expectException(\Exception::class);
        foreach ($client->fetch($resource, $query, $binds) as $r) {
            // nop
        }
    }

    public function testFetchRange()
    {
        $client = $this->createWeqClient('weq:4000');

        $resource = 'weqdb1';
        $query    = 'SELECT title, price FROM m_product WHERE series_id = :series_id';
        $offset   = 0;
        $limit    = 2;
        $binds = [
            'series_id' => '3000',
        ];

        $rows = $client->fetchRange($resource, $query, $offset, $limit, $binds);

        $this->assertEquals(2, count($rows));
    }

    public function testFetchRangeThrowException()
    {
        $client = $this->createWeqClient('nothing-weq:4000');

        $resource = 'weqdb1';
        $query    = 'SELECT title, price FROM m_product WHERE series_id = :series_id';
        $offset   = 0;
        $limit    = 2;
        $binds = [
            'series_id' => '3000',
        ];

        $this->expectException(\Exception::class);
        $client->fetchRange($resource, $query, $offset, $limit, $binds);
    }

    public function testCount()
    {
        $client = $this->createWeqClient('weq:4000');

        $resource = 'weqdb1';
        $query    = 'SELECT title, price FROM m_product WHERE series_id = :series_id';
        $binds = [
            'series_id' => '3000',
        ];

        $this->assertEquals(3, $client->count($resource, $query, $binds));
    }

    public function testCountThrowException()
    {
        $client = $this->createWeqClient('nothing-weq:4000');

        $resource = 'weqdb1';
        $query    = 'SELECT title, price FROM m_product WHERE series_id = :series_id';
        $binds = [
            'series_id' => '3000',
        ];

        $this->expectException(\Exception::class);
        $client->count($resource, $query, $binds);
    }

    private function createWeqClient($host)
    {
        $config = Config::create($host)->setSecure(false);

        return Client::create($config);
    }
}
