<?php declare(strict_types = 1);

namespace Mihaeu\ProductConfigurator;

require 'CreateMoneyTrait.php';

class BidTest extends \PHPUnit_Framework_TestCase
{
    use CreateMoneyTrait;

    public function testReturnsBid()
    {
        $money = $this->createMoney();
        $bid = new Bid($money, 'John');
        $this->assertEquals($money, $bid->bid());
    }

    public function testReturnsUser()
    {
        $bid = new Bid($this->createMoney(), 'John');
        $this->assertEquals('John', $bid->user());
    }
}