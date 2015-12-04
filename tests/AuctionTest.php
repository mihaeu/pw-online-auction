<?php declare(strict_types = 1);

/**
 * @covers Auction
 * @covers BidCollection
 * @uses AuctionTitle
 * @uses Money
 * @uses Currency
 * @uses Bid
 * @uses BidCollection
 */
class AuctionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var DateTimeImmutable
     */
    private $now;

    /**
     * @var AuctionTitle
     */
    private $title;

    public function setUp()
    {
        $this->now = new DateTimeImmutable();
        $this->title = new AuctionTitle('Test');
    }

    public function testUserCanPlaceBid()
    {
        $auction = new Auction($this->title, '', $this->now, $this->now, '');
        $bid1 = new Bid(new Money(1, new Currency('EUR')), 'John');
        $bid2 = new Bid(new Money(2, new Currency('EUR')), 'Mary');
        $auction->addBidFromUser($bid1);
        $auction->addBidFromUser($bid2);
        $this->assertEquals($bid2, $auction->highestBid());
    }

    public function testOwnerCannotPlaceBids()
    {
        $owner = 'John Doe';
        $auction = new Auction($this->title, '', $this->now, $this->now, $owner);

        $this->setExpectedExceptionRegExp(
            InvalidArgumentException::class,
            '/Auction owner cannot place bids/'
        );
        $auction->addBidFromUser(new Bid(new Money(1, new Currency('EUR')), $owner));
    }

    public function testBidHasToBeHigherThanPreviouslyHighestBid()
    {
        $auction = new Auction($this->title, '', $this->now, $this->now, '');

        $auction->addBidFromUser(new Bid(new Money(100, new Currency('EUR')), 'John'));
        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/Bid must be higher than highest bid/');
        $auction->addBidFromUser(new Bid(new Money(1, new Currency('EUR')), 'Mary'));
    }

    public function testFindsHighestBidder()
    {
        $auction = new Auction($this->title, '', $this->now, $this->now, '');

        $this->setExpectedExceptionRegExp(Exception::class, '/No bids/');
        $auction->highestBid();
    }

    public function testCannotBidBeforeAuctionStart()
    {
        $tomorrow = new DateTimeImmutable('tomorrow');
        $auction = new Auction($this->title, '', $tomorrow, $this->now, '');

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/started/');
        $auction->addBidFromUser(new Bid(new Money(1, new Currency('EUR')), 'John'));
    }

    public function testCannotBidAfterAuction()
    {
        $yesterday = new DateTimeImmutable('yesterday');
        $auction = new Auction($this->title, '', $this->now, $yesterday, '');

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/finished/');
        $auction->addBidFromUser(new Bid(new Money(1, new Currency('EUR')), 'John'));
    }
}
