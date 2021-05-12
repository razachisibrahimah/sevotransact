<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use RazakIbrahimah\Sevotransact\MomoCollection;
use function Prinx\Dotenv\env;

class MomoCollectionTest extends TestCase
{
    public function testMomoCollection()
    {
        $momo = new MomoCollection;

        $amount = 0.2;
        $phone = env('TEST_MSISDN');
        $channel = env('TEST_CHANNEL');

        $response = $momo->pay($amount, $phone, $channel);
        $this->assertIsArray($response);
        $this->assertEquals(200, $response['status']);
        $this->assertEquals('success', $response['message']);
        $this->assertEquals($momo->getTransactionId(), $response['data']['client-reference']);
        $this->assertEquals('100', $response['data']['code']);
        $this->assertEquals($phone, $response['data']['msisdn']);
    }
}
