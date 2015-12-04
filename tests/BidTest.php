<?php declare(strict_types = 1);

/**
 * @covers Bid
 * @uses Money
 * @uses Currency
 */
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

    public function testComparesBids()
    {
        $bid1 = new Bid(new Money(1, new Currency('EUR')), 'John');
        $bid2 = new Bid(new Money(2, new Currency('EUR')), 'John');
        $this->assertTrue($bid2->isHigherThan($bid1));
    }
}