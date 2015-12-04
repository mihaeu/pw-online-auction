<?php declare(strict_types = 1);

/**
 * @covers BidCollection
 * @uses Money
 * @uses Currency
 * @uses Bid
 */
class BidCollectionTest extends \PHPUnit_Framework_TestCase
{
    use CreateMoneyTrait;

    public function testOneBidIsHighestBid()
    {
        $bidCollection = new BidCollection();
        $bid = new Bid($this->createMoney(), 'John');
        $bidCollection->addBid($bid);
        $this->assertEquals($bid, $bidCollection->findHighest());
    }

    public function testFindHighestBid()
    {
        $bidCollection = new BidCollection();
        $bid2 = new Bid(new Money(2, new Currency('EUR')), 'Mary');
        $bid1 = new Bid(new Money(1, new Currency('EUR')), 'John');
        $bidCollection->addBid($bid1);
        $bidCollection->addBid($bid2);
        $this->assertEquals($bid2, $bidCollection->findHighest());
    }
}
