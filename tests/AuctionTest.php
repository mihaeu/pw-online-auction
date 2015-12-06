<?php declare(strict_types = 1);

/**
 * @covers Auction
 * @uses AuctionTitle
 * @uses AuctionDescription
 * @uses Money
 * @uses Currency
 * @uses Bid
 * @uses BidCollection
 */
class AuctionTest extends BaseTestCase
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
        $auction = new Auction($this->title, $this->desc, $this->mockInterval(), $this->startPrice, $this->mockUser());
        $bid1 = new Bid(new Money(1, new Currency('EUR')), $this->mockUser());
        $bid2 = new Bid(new Money(2, new Currency('EUR')), $this->mockUser());
        $auction->placeBid($bid1);
        $auction->placeBid($bid2);
        $this->assertEquals($bid2, $auction->highestBid());
    }

    public function testOwnerCannotPlaceBids()
    {
        $owner = $this->mockUser();
        $owner->method('equals')->willReturn(true);
        $auction = new Auction($this->title, $this->desc, $this->mockInterval(), $this->startPrice, $owner);

        $this->setExpectedExceptionRegExp(
            InvalidArgumentException::class,
            '/Auction owner cannot place bids/'
        );
        $auction->placeBid(new Bid(new Money(1, new Currency('EUR')), $owner));
    }

    public function testBidHasToBeHigherThanPreviouslyHighestBid()
    {
        $auction = new Auction($this->title, $this->desc, $this->mockInterval(), $this->startPrice, $this->mockUser());

        $auction->placeBid(new Bid(new Money(100, new Currency('EUR')), $this->mockUser()));
        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/Bid must be higher than highest bid/');
        $auction->placeBid(new Bid(new Money(1, new Currency('EUR')), $this->mockUser()));
    }

    public function testFindsHighestBidder()
    {
        $auction = new Auction($this->title, $this->desc, $this->mockInterval(), $this->startPrice, $this->mockUser());

        $this->setExpectedExceptionRegExp(Exception::class, '/No bids/');
        $auction->highestBid();
    }

    public function testCannotBidBeforeAuctionStart()
    {
        $interval = $this->getMockBuilder('AuctionInterval')->disableOriginalConstructor()->getMock();
        $interval->method('dateIsInInterval')->willReturn(-1);
        $auction = new Auction($this->title, $this->desc, $interval, $this->startPrice, $this->mockUser());

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/started/');
        $auction->placeBid(new Bid(new Money(1, new Currency('EUR')), $this->mockUser()));
    }

    public function testCannotBidAfterAuction()
    {
        $interval = $this->getMockBuilder('AuctionInterval')->disableOriginalConstructor()->getMock();
        $interval->method('dateIsInInterval')->willReturn(1);
        $auction = new Auction($this->title, $this->desc, $interval, $this->startPrice, $this->mockUser());

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/finished/');
        $auction->placeBid(new Bid(new Money(1, new Currency('EUR')), $this->mockUser()));
    }

    public function testStartPriceHasToBePositive()
    {
        $startPrice = new Money(-10, new Currency('EUR'));

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/positive/');
        new Auction($this->title, $this->desc, $this->mockInterval(), $startPrice, $this->mockUser());
    }

    public function testBidHasToBeHigherThanStartPrice()
    {
        $startPrice = new Money(10, new Currency('EUR'));
        $auction = new Auction($this->title, $this->desc, $this->mockInterval(), $startPrice, $this->mockUser());

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/higher.*start/');
        $auction->placeBid(new Bid($this->oneEuro(), $this->mockUser()));
    }

    public function testCanActivateInstantBuy()
    {
        $seller = $this->mockUser();
        $auction = new Auction($this->title, $this->desc, $this->mockInterval(), $this->startPrice, $seller);
        $auction->placeBid(new Bid($this->tenEuro(), $this->mockUser()));
        $auction->setInstantBuyPrice($this->hundredEuro());

        $buyer = $this->mockUser();
        $buyer->method('equals')->willReturn(false);

        $auction->instantBuy($buyer);
        $this->assertEquals($buyer, $auction->winner());
    }

    public function testSellerCannotInstantBuy()
    {
        $seller = $this->mockUser();
        $auction = new Auction($this->title, $this->desc, $this->mockInterval(), $this->startPrice, $seller);
        $auction->setInstantBuyPrice($this->hundredEuro());

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/Seller cannot instant buy/');
        $seller->method('equals')->willReturn(true);
        $auction->instantBuy($seller);
    }

    public function testCannotSetInstantBuyLowerThanHighestBid()
    {
        $seller = $this->mockUser();
        $auction = new Auction($this->title, $this->desc, $this->mockInterval(), $this->startPrice, $seller);
        $auction->placeBid(new Bid($this->hundredEuro(), $this->mockUser()));

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/instant buy has to be higher than highest bid/i');
        $auction->setInstantBuyPrice($this->tenEuro());
    }

    public function testInstantBuyPriceHasToBeHigherThanStartPrice()
    {
        $auction = new Auction($this->title, $this->desc, $this->mockInterval(), $this->tenEuro(), $this->mockUser());

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/Instant buy price has to be higher/');
        $auction->setInstantBuyPrice($this->oneEuro());
    }

    public function testCannotInstantBuyWithoutInstantBuyOption()
    {
        $auction = new Auction($this->title, $this->desc, $this->mockInterval(), $this->tenEuro(), $this->mockUser());

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/instant buy price has not been set/');
        $auction->instantBuy($this->mockUser());
    }

    public function testCannotBidAfterAuctionIsWon()
    {
        $auction = new Auction($this->title, $this->desc, $this->mockInterval(), $this->startPrice, $this->mockUser());
        $auction->setInstantBuyPrice($this->tenEuro());
        $auction->instantBuy($this->mockUser());

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/Auction has already been won/');
        $auction->placeBid(new Bid($this->hundredEuro(), $this->mockUser()));
    }

    public function testCannotInstantBuyAfterAuctionIsWon()
    {
        $auction = new Auction($this->title, $this->desc, $this->mockInterval(), $this->startPrice, $this->mockUser());
        $auction->setInstantBuyPrice($this->tenEuro());
        $auction->instantBuy($this->mockUser());

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/Auction has already been won/');
        $auction->instantBuy($this->mockUser());
    }

    public function testInstantBuyPriceCanBeLowered()
    {
        $seller = $this->mockUser();
        $auction = new Auction($this->title, $this->desc, $this->mockInterval(), $this->startPrice, $seller);
        $auction->setInstantBuyPrice($this->hundredEuro());
        $auction->setInstantBuyPrice($this->tenEuro());

        $buyer = $this->mockUser();
        $buyer->method('equals')->willReturn(false);

        $auction->instantBuy($buyer);
        $this->assertEquals($buyer, $auction->winner());
    }

    public function testInstantBuyPriceCannotBeIncreased()
    {
        $seller = $this->mockUser();
        $auction = new Auction($this->title, $this->desc, $this->mockInterval(), $this->startPrice, $seller);
        $auction->setInstantBuyPrice($this->tenEuro());

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/only be changed if new price is lower/');
        $auction->setInstantBuyPrice($this->hundredEuro());
    }

    public function testInstantBuyOnlyAfterAuctionStart()
    {
        $interval = $this->getMockBuilder('AuctionInterval')->disableOriginalConstructor()->getMock();
        $interval->method('dateIsInInterval')->willReturn(-1);
        $auction = new Auction($this->title, $this->desc, $interval, $this->startPrice, $this->mockUser());
        $auction->setInstantBuyPrice($this->tenEuro());

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/auction has not started yet/i');
        $auction->instantBuy($this->mockUser());
    }

    public function testCanChangeStartPriceBeforeBidsHaveBeenPlaced()
    {
        $seller = $this->mockUser();
        $auction = new Auction($this->title, $this->desc, $this->mockInterval(), $this->hundredEuro(), $seller);
        $auction->setStartPrice($this->oneEuro());

        // this only works because the start price could be lowered
        $auction->placeBid(new Bid($this->tenEuro(), $this->mockUser()));
        $this->assertEquals($this->tenEuro(), $auction->highestBid()->bid());
    }

    public function testCannotChangeStartPriceAfterBidsHaveBeenPlaced()
    {
        $seller = $this->mockUser();
        $auction = new Auction($this->title, $this->desc, $this->mockInterval(), $this->startPrice, $seller);
        $auction->placeBid(new Bid($this->hundredEuro(), $this->mockUser()));

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/Cannot change start price after bids have been placed/');
        $auction->setStartPrice($this->tenEuro());
    }

    public function testStartPriceCanOnlyBeLowered()
    {
        $seller = $this->mockUser();
        $auction = new Auction($this->title, $this->desc, $this->mockInterval(), $this->startPrice, $seller);

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/Start price can only be lowered/');
        $auction->setStartPrice($this->tenEuro());
    }

    public function testCannotCloseAfterBiddingHasStarted()
    {
        $auction = new Auction($this->title, $this->desc, $this->mockInterval(), $this->startPrice, $this->mockUser());
        $auction->placeBid(new Bid($this->tenEuro(), $this->mockUser()));

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/Cannot close auction after bidding has started/');
        $auction->close();
    }

    public function testCannotBidAfterAuctionClosed()
    {
        $auction = new Auction($this->title, $this->desc, $this->mockInterval(), $this->startPrice, $this->mockUser());
        $auction->close();

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/auction.*closed/i');
        $auction->placeBid(new Bid($this->tenEuro(), $this->mockUser()));
    }

    public function testCannotInstantBuyAfterAuctionClosed()
    {
        $auction = new Auction($this->title, $this->desc, $this->mockInterval(), $this->startPrice, $this->mockUser());
        $auction->close();

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/auction.*closed/i');
        $auction->instantBuy($this->mockUser());
    }
}
