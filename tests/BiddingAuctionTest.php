<?php declare(strict_types = 1);

/**
 * @covers BiddingAuction
 * @uses AuctionTitle
 * @uses AuctionDescription
 * @uses Money
 * @uses Currency
 * @uses Bid
 * @uses BidCollection
 */
class BiddingAuctionTest extends BaseTestCase
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
        $auction = new BiddingAuction(
            $this->title,
            $this->desc,
            $this->mockInterval(),
            $this->startPrice,
            $this->mockUser()
        );
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
        $auction = new BiddingAuction(
            $this->title,
            $this->desc,
            $this->mockInterval(),
            $this->startPrice,
            $owner
        );

        $this->setExpectedExceptionRegExp(
            InvalidArgumentException::class,
            '/Seller cannot buy/i'
        );
        $auction->placeBid(new Bid(new Money(1, new Currency('EUR')), $owner));
    }

    public function testBidHasToBeHigherThanPreviouslyHighestBid()
    {
        $auction = new BiddingAuction(
            $this->title,
            $this->desc,
            $this->mockInterval(),
            $this->startPrice,
            $this->mockUser()
        );

        $auction->placeBid(new Bid(new Money(100, new Currency('EUR')), $this->mockUser()));
        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/Bid must be higher than highest bid/');
        $auction->placeBid(new Bid(new Money(1, new Currency('EUR')), $this->mockUser()));
    }

    public function testFindsHighestBidder()
    {
        $auction = new BiddingAuction(
            $this->title,
            $this->desc,
            $this->mockInterval(),
            $this->startPrice,
            $this->mockUser()
        );

        $this->setExpectedExceptionRegExp(Exception::class, '/No bids/');
        $auction->highestBid();
    }

    public function testCannotBidBeforeAuctionStart()
    {
        $interval = $this->getMockBuilder('AuctionInterval')->disableOriginalConstructor()->getMock();
        $interval->method('dateIsInInterval')->willReturn(-1);
        $auction = new BiddingAuction(
            $this->title,
            $this->desc,
            $interval,
            $this->startPrice,
            $this->mockUser()
        );

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/started/');
        $auction->placeBid(new Bid(new Money(1, new Currency('EUR')), $this->mockUser()));
    }

    public function testCannotBidAfterAuction()
    {
        $interval = $this->getMockBuilder('AuctionInterval')->disableOriginalConstructor()->getMock();
        $interval->method('dateIsInInterval')->willReturn(1);
        $auction = new BiddingAuction(
            $this->title,
            $this->desc,
            $interval,
            $this->startPrice,
            $this->mockUser()
        );

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/finished/');
        $auction->placeBid(new Bid(new Money(1, new Currency('EUR')), $this->mockUser()));
    }

    public function testStartPriceHasToBePositive()
    {
        $startPrice = new Money(-10, new Currency('EUR'));

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/positive/');
        new BiddingAuction(
            $this->title,
            $this->desc,
            $this->mockInterval(),
            $startPrice,
            $this->mockUser()
        );
    }

    public function testBidHasToBeHigherThanStartPrice()
    {
        $startPrice = new Money(10, new Currency('EUR'));
        $auction = new BiddingAuction(
            $this->title,
            $this->desc,
            $this->mockInterval(),
            $startPrice,
            $this->mockUser()
        );

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/higher.*start/');
        $auction->placeBid(new Bid($this->oneEuro(), $this->mockUser()));
    }

    public function testCanChangeStartPriceBeforeBidsHaveBeenPlaced()
    {
        $seller = $this->mockUser();
        $auction = new BiddingAuction(
            $this->title,
            $this->desc,
            $this->mockInterval(),
            $this->hundredEuro(),
            $seller
        );
        $auction->setStartPrice($this->oneEuro());

        // this only works because the start price could be lowered
        $auction->placeBid(new Bid($this->tenEuro(), $this->mockUser()));
        $this->assertEquals($this->tenEuro(), $auction->highestBid()->bid());
    }

    public function testCannotChangeStartPriceAfterBidsHaveBeenPlaced()
    {
        $seller = $this->mockUser();
        $auction = new BiddingAuction(
            $this->title,
            $this->desc,
            $this->mockInterval(),
            $this->startPrice,
            $seller
        );
        $auction->placeBid(new Bid($this->hundredEuro(), $this->mockUser()));

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/Cannot change start price after bids have been placed/');
        $auction->setStartPrice($this->tenEuro());
    }

    public function testStartPriceCanOnlyBeLowered()
    {
        $seller = $this->mockUser();
        $auction = new BiddingAuction(
            $this->title,
            $this->desc,
            $this->mockInterval(),
            $this->startPrice,
            $seller
        );

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/Start price can only be lowered/');
        $auction->setStartPrice($this->tenEuro());
    }

    public function testCannotCloseAfterBiddingHasStarted()
    {
        $auction = new BiddingAuction(
            $this->title,
            $this->desc,
            $this->mockInterval(),
            $this->startPrice,
            $this->mockUser()
        );
        $auction->placeBid(new Bid($this->tenEuro(), $this->mockUser()));

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/Cannot close auction after bidding has started/');
        $auction->close();
    }

    public function testCannotBidAfterAuctionClosed()
    {
        $auction = new BiddingAuction(
            $this->title,
            $this->desc,
            $this->mockInterval(),
            $this->startPrice,
            $this->mockUser()
        );
        $auction->close();

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/auction.*closed/i');
        $auction->placeBid(new Bid($this->tenEuro(), $this->mockUser()));
    }

    public function testReturnsWinnerAfterAuctionEnd()
    {
        $interval = $this->mockInterval();
        $interval->method('dateIsInInterval')->will($this->onConsecutiveCalls(
            0, // 1st bid start time check
            0, // 1st bid end time check
            0, // 2nd bid start time check
            0, // 2nd bid end time check
            1  // auction finished when checking for winner
        ));
        $auction = new BiddingAuction(
            $this->title,
            $this->desc,
            $interval,
            $this->startPrice,
            $this->mockUser()
        );

        $highestBidder = $this->mockUser('mail@highest.com');
        $auction->placeBid(new Bid($this->tenEuro(), $this->mockUser()));
        $auction->placeBid(new Bid($this->hundredEuro(), $highestBidder));
        $this->assertEquals($highestBidder, $auction->winner());

        return $auction;
    }

    /**
     * @depends testReturnsWinnerAfterAuctionEnd
     *
     * @param BiddingAuction $auction
     */
    public function testCannotPlaceBidAfterAuctionHasBeenWon(BiddingAuction $auction)
    {
        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/Auction has.*won/');
        $auction->placeBid(new Bid($this->hundredEuro(), $this->mockUser()));
    }
}
