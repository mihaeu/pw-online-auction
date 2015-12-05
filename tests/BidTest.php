<?php declare(strict_types = 1);

/**
 * @covers Bid
 * @uses Money
 * @uses Currency
 */
class BidTest extends BaseTestCase
{
    use CreateMoneyTrait;

    public function testReturnsBid()
    {
        $money = $this->createMoney();
        $bid = new Bid($money, $this->mockUser());
        $this->assertEquals($money, $bid->bid());
    }

    public function testReturnsUser()
    {
        $bid = new Bid($this->createMoney(), $this->mockUser());
        $this->assertEquals($this->mockUser(), $bid->bidder());
    }

    public function testComparesBids()
    {
        $bid1 = new Bid(new Money(1, new Currency('EUR')), $this->mockUser());
        $bid2 = new Bid(new Money(2, new Currency('EUR')), $this->mockUser());
        $this->assertTrue($bid2->isHigherThan($bid1));
    }

    public function testBidIsPositive()
    {
        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/Bid must be higher than 0/');
        new Bid(new Money(0, new Currency('EUR')), $this->mockUser());
    }
}