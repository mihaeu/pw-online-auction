<?php declare(strict_types = 1);

/**
 * @covers Auction
 * @covers BidCollection
 * @uses AuctionTitle
 * @uses AuctionDescription
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

    private $desc;
    private $startPrice;

    public function setUp()
    {
        $this->now = new DateTimeImmutable();
        $this->title = new AuctionTitle('Test');
        $this->desc = new AuctionDescription('.................');
        $this->startPrice = new Money(1, new Currency('EUR'));
    }

    public function testUserCanPlaceBid()
    {
        $auction = new Auction($this->title, $this->desc, $this->now, $this->now, $this->startPrice, '');
        $bid1 = new Bid(new Money(1, new Currency('EUR')), 'John');
        $bid2 = new Bid(new Money(2, new Currency('EUR')), 'Mary');
        $auction->addBidFromUser($bid1);
        $auction->addBidFromUser($bid2);
        $this->assertEquals($bid2, $auction->highestBid());
    }

    public function testOwnerCannotPlaceBids()
    {
        $owner = 'John Doe';
        $auction = new Auction($this->title, $this->desc, $this->now, $this->now, $this->startPrice, $owner);

        $this->setExpectedExceptionRegExp(
            InvalidArgumentException::class,
            '/Auction owner cannot place bids/'
        );
        $auction->addBidFromUser(new Bid(new Money(1, new Currency('EUR')), $owner));
    }

    public function testBidHasToBeHigherThanPreviouslyHighestBid()
    {
        $auction = new Auction($this->title, $this->desc, $this->now, $this->now, $this->startPrice, '');

        $auction->addBidFromUser(new Bid(new Money(100, new Currency('EUR')), 'John'));
        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/Bid must be higher than highest bid/');
        $auction->addBidFromUser(new Bid(new Money(1, new Currency('EUR')), 'Mary'));
    }

    public function testFindsHighestBidder()
    {
        $auction = new Auction($this->title, $this->desc, $this->now, $this->now, $this->startPrice, '');

        $this->setExpectedExceptionRegExp(Exception::class, '/No bids/');
        $auction->highestBid();
    }

    public function testCannotBidBeforeAuctionStart()
    {
        $tomorrow = new DateTimeImmutable('tomorrow');
        $auction = new Auction($this->title, $this->desc, $tomorrow, $this->now, $this->startPrice, '');

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/started/');
        $auction->addBidFromUser(new Bid(new Money(1, new Currency('EUR')), 'John'));
    }

    public function testCannotBidAfterAuction()
    {
        $yesterday = new DateTimeImmutable('yesterday');
        $auction = new Auction($this->title, $this->desc, $this->now, $yesterday, $this->startPrice, '');

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/finished/');
        $auction->addBidFromUser(new Bid(new Money(1, new Currency('EUR')), 'John'));
    }

    public function testStartPriceHasToBePositive()
    {
        $startPrice = new Money(-10, new Currency('EUR'));

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/positive/');
        new Auction($this->title, $this->desc, $this->now, $this->now, $startPrice, '');
    }

    public function testBidHasToBeHigherThanStartPrice()
    {
        $startPrice = new Money(10, new Currency('EUR'));
        $auction = new Auction($this->title, $this->desc, $this->now, $this->now, $startPrice, '');

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/higher.*start/');
        $auction->addBidFromUser(new Bid(new Money(1, new Currency('EUR')), 'John'));
    }
}
