<?php declare(strict_types = 1);

/**
 * @covers BidCollection
 * @uses Money
 * @uses Currency
 * @uses Bid
 */
class BidCollectionTest extends BaseTestCase
{
    use CreateMoneyTrait;

    public function testHighestBidWithoutBidsIs0()
    {
        $bidCollection = new BidCollection();
        $this->assertEquals(0, $bidCollection->findHighest());
    }

    public function testOneBidIsHighestBid()
    {
        $bidCollection = new BidCollection();
        $bid = new Bid($this->createMoney(), $this->mockUser());
        $bidCollection->addBid($bid);
        $this->assertEquals($bid, $bidCollection->findHighest());
    }

    public function testFindHighestBid()
    {
        $bidCollection = new BidCollection();
        $bid2 = new Bid(new Money(2, new Currency('EUR')), $this->mockUser());
        $bid1 = new Bid(new Money(1, new Currency('EUR')), $this->mockUser());
        $bidCollection->addBid($bid1);
        $bidCollection->addBid($bid2);
        $this->assertEquals($bid2, $bidCollection->findHighest());
    }

    public function testDetectsWhenEmpty()
    {
        $this->assertFalse((new BidCollection())->hasBids());
    }

    public function testDetectsWhenNotEmpty()
    {
        $bidCollection = new BidCollection();
        $bidCollection->addBid(new Bid(new Money(2, new Currency('EUR')), $this->mockUser()));
        $this->assertTrue($bidCollection->hasBids());
    }
}
