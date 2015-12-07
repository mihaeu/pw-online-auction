<?php declare(strict_types = 1);

class InstantBuyAuctionTest extends BaseTestCase
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
    
    public function testCanActivateInstantBuy()
    {
        $seller = $this->mockUser();
        $auction = new InstantBuyAuction($this->title, $this->desc, $this->mockInterval(), $this->startPrice, $seller);
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
        $auction = new InstantBuyAuction($this->title, $this->desc, $this->mockInterval(), $this->startPrice, $seller);
        $auction->setInstantBuyPrice($this->hundredEuro());

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/Seller cannot buy/i');
        $seller->method('equals')->willReturn(true);
        $auction->instantBuy($seller);
    }

    public function testCannotSetInstantBuyLowerThanHighestBid()
    {
        $seller = $this->mockUser();
        $auction = new InstantBuyAuction($this->title, $this->desc, $this->mockInterval(), $this->startPrice, $seller);
        $auction->placeBid(new Bid($this->hundredEuro(), $this->mockUser()));

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/instant buy has to be higher than highest bid/i');
        $auction->setInstantBuyPrice($this->tenEuro());
    }

    public function testInstantBuyPriceHasToBeHigherThanStartPrice()
    {
        $auction = new InstantBuyAuction($this->title, $this->desc, $this->mockInterval(), $this->tenEuro(), $this->mockUser());

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/Instant buy price has to be higher/');
        $auction->setInstantBuyPrice($this->oneEuro());
    }

    public function testCannotInstantBuyWithoutInstantBuyOption()
    {
        $auction = new InstantBuyAuction($this->title, $this->desc, $this->mockInterval(), $this->tenEuro(), $this->mockUser());

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/instant buy price has not been set/');
        $auction->instantBuy($this->mockUser());
    }

    public function testCannotBidAfterAuctionIsWon()
    {
        $auction = new InstantBuyAuction($this->title, $this->desc, $this->mockInterval(), $this->startPrice, $this->mockUser());
        $auction->setInstantBuyPrice($this->tenEuro());
        $auction->instantBuy($this->mockUser());

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/Auction has already been won/');
        $auction->placeBid(new Bid($this->hundredEuro(), $this->mockUser()));
    }

    public function testCannotInstantBuyAfterAuctionIsWon()
    {
        $auction = new InstantBuyAuction($this->title, $this->desc, $this->mockInterval(), $this->startPrice, $this->mockUser());
        $auction->setInstantBuyPrice($this->tenEuro());
        $auction->instantBuy($this->mockUser());

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/Auction has already been won/');
        $auction->instantBuy($this->mockUser());
    }

    public function testInstantBuyPriceCanBeLowered()
    {
        $seller = $this->mockUser();
        $auction = new InstantBuyAuction($this->title, $this->desc, $this->mockInterval(), $this->startPrice, $seller);
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
        $auction = new InstantBuyAuction($this->title, $this->desc, $this->mockInterval(), $this->startPrice, $seller);
        $auction->setInstantBuyPrice($this->tenEuro());

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/only be changed if new price is lower/');
        $auction->setInstantBuyPrice($this->hundredEuro());
    }

    public function testInstantBuyOnlyAfterAuctionStart()
    {
        $interval = $this->getMockBuilder('AuctionInterval')->disableOriginalConstructor()->getMock();
        $interval->method('dateIsInInterval')->willReturn(-1);
        $auction = new InstantBuyAuction($this->title, $this->desc, $interval, $this->startPrice, $this->mockUser());
        $auction->setInstantBuyPrice($this->tenEuro());

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/auction has not started yet/i');
        $auction->instantBuy($this->mockUser());
    }

    public function testCannotInstantBuyAfterAuctionClosed()
    {
        $auction = new InstantBuyAuction($this->title, $this->desc, $this->mockInterval(), $this->startPrice, $this->mockUser());
        $auction->close();

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/auction.*closed/i');
        $auction->instantBuy($this->mockUser());
    }
}
