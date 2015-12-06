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
        $auction = new Auction($this->title, $this->desc, $this->now, $this->now, $this->startPrice, $this->mockUser());
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
        $auction = new Auction($this->title, $this->desc, $this->now, $this->now, $this->startPrice, $owner);

        $this->setExpectedExceptionRegExp(
            InvalidArgumentException::class,
            '/Auction owner cannot place bids/'
        );
        $auction->placeBid(new Bid(new Money(1, new Currency('EUR')), $owner));
    }

    public function testBidHasToBeHigherThanPreviouslyHighestBid()
    {
        $auction = new Auction($this->title, $this->desc, $this->now, $this->now, $this->startPrice, $this->mockUser());

        $auction->placeBid(new Bid(new Money(100, new Currency('EUR')), $this->mockUser()));
        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/Bid must be higher than highest bid/');
        $auction->placeBid(new Bid(new Money(1, new Currency('EUR')), $this->mockUser()));
    }

    public function testFindsHighestBidder()
    {
        $auction = new Auction($this->title, $this->desc, $this->now, $this->now, $this->startPrice, $this->mockUser());

        $this->setExpectedExceptionRegExp(Exception::class, '/No bids/');
        $auction->highestBid();
    }

    public function testCannotBidBeforeAuctionStart()
    {
        $tomorrow = new DateTimeImmutable('tomorrow');
        $auction = new Auction($this->title, $this->desc, $tomorrow, $this->now, $this->startPrice, $this->mockUser());

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/started/');
        $auction->placeBid(new Bid(new Money(1, new Currency('EUR')), $this->mockUser()));
    }

    public function testCannotBidAfterAuction()
    {
        $yesterday = new DateTimeImmutable('yesterday');
        $auction = new Auction($this->title, $this->desc, $this->now, $yesterday, $this->startPrice, $this->mockUser());

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/finished/');
        $auction->placeBid(new Bid(new Money(1, new Currency('EUR')), $this->mockUser()));
    }

    public function testStartPriceHasToBePositive()
    {
        $startPrice = new Money(-10, new Currency('EUR'));

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/positive/');
        new Auction($this->title, $this->desc, $this->now, $this->now, $startPrice, $this->mockUser());
    }

    public function testBidHasToBeHigherThanStartPrice()
    {
        $startPrice = new Money(10, new Currency('EUR'));
        $auction = new Auction($this->title, $this->desc, $this->now, $this->now, $startPrice, $this->mockUser());

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/higher.*start/');
        $auction->placeBid(new Bid($this->oneEuro(), $this->mockUser()));
    }

    public function testCanActivateInstantBuy()
    {
        $seller = $this->mockUser();
        $auction = new Auction($this->title, $this->desc, $this->now, $this->now, $this->startPrice, $seller);
        $auction->setInstantBuyPrice($this->hundredEuro());

        $buyer = $this->mockUser();
        $buyer->method('equals')->willReturn(false);

        $auction->instantBuy($buyer);
        $this->assertEquals($buyer, $auction->winner());
    }

    public function testSellerCannotInstantBuy()
    {
        $seller = $this->mockUser();
        $auction = new Auction($this->title, $this->desc, $this->now, $this->now, $this->startPrice, $seller);
        $auction->setInstantBuyPrice($this->hundredEuro());

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/Seller cannot instant buy/');
        $seller->method('equals')->willReturn(true);
        $auction->instantBuy($seller);
    }

    public function testCannotActivateInstantBuyAfterBiddingStarted()
    {
        $seller = $this->mockUser();
        $auction = new Auction($this->title, $this->desc, $this->now, $this->now, $this->startPrice, $seller);
        $auction->placeBid(new Bid($this->startPrice, $this->mockUser()));

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/Cannot.*bidding.*started/');
        $auction->setInstantBuyPrice($this->hundredEuro());
    }

    public function testInstantBuyPriceHasToBeHigherThanStartPrice()
    {
        $auction = new Auction($this->title, $this->desc, $this->now, $this->now, $this->tenEuro(), $this->mockUser());

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/Instant buy price has to be higher/');
        $auction->setInstantBuyPrice($this->oneEuro());
    }

    public function testCannotInstantBuyWithoutInstantBuyOption()
    {
        $auction = new Auction($this->title, $this->desc, $this->now, $this->now, $this->tenEuro(), $this->mockUser());

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/instant buy price has not been set/');
        $auction->instantBuy($this->mockUser());
    }

    public function testInstantBuyPriceCanBeLowered()
    {
        $seller = $this->mockUser();
        $auction = new Auction($this->title, $this->desc, $this->now, $this->now, $this->startPrice, $seller);
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
        $auction = new Auction($this->title, $this->desc, $this->now, $this->now, $this->startPrice, $seller);
        $auction->setInstantBuyPrice($this->tenEuro());

        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/only be changed if new price is lower/');
        $auction->setInstantBuyPrice($this->hundredEuro());
    }
}
