<?php declare(strict_types = 1);

/**
 * @covers BiddingAndInstantBuyAuction
 *
 * @uses BiddingAuction
 * @uses Currency
 * @uses Money
 * @uses AuctionTitle
 * @uses Bid
 * @uses AuctionDescription
 * @uses BidCollection
 */
class BiddingAndInstantBuyAuctionTest extends PHPUnit_Framework_TestCase
{
    use MoneyHelperTrait;
    use UserHelperTrait;
    use AuctionHelperTrait;

    /**
     * @var AuctionTitle
     */
    private $title;

    /**
     * @var AuctionDescription
     */
    private $desc;

    /**
     * @var Money
     */
    private $startPrice;

    public function setUp()
    {
        $this->title = new AuctionTitle('Test');
        $this->desc = new AuctionDescription('.................');
        $this->startPrice = new Money(1, new Currency('EUR'));
    }

    public function testCanActivateInstantBuy()
    {
        $seller = $this->mockUser();
        $auction = new BiddingAndInstantBuyAuction(
            $this->title,
            $this->desc,
            $this->mockInterval(),
            $this->startPrice,
            $seller
        );
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
        $auction = new BiddingAndInstantBuyAuction(
            $this->title,
            $this->desc,
            $this->mockInterval(),
            $this->startPrice,
            $seller
        );
        $auction->setInstantBuyPrice($this->hundredEuro());

        $this->setExpectedException(
            InvalidArgumentException::class,
            'Seller cannot buy from himself'
        );
        $seller->method('equals')->willReturn(true);
        $auction->instantBuy($seller);
    }

    public function testCannotSetInstantBuyLowerThanHighestBid()
    {
        $seller = $this->mockUser();
        $auction = new BiddingAndInstantBuyAuction(
            $this->title,
            $this->desc,
            $this->mockInterval(),
            $this->startPrice,
            $seller
        );
        $auction->placeBid(new Bid($this->hundredEuro(), $this->mockUser()));

        $this->setExpectedException(
            InvalidArgumentException::class,
            'Instant buy has to be higher than highest bid'
        );
        $auction->setInstantBuyPrice($this->tenEuro());
    }

    public function testInstantBuyPriceHasToBeHigherThanStartPrice()
    {
        $auction = new BiddingAndInstantBuyAuction(
            $this->title,
            $this->desc,
            $this->mockInterval(),
            $this->tenEuro(),
            $this->mockUser()
        );

        $this->setExpectedException(
            InvalidArgumentException::class,
            'Instant buy price has to be higher than start price'
        );
        $auction->setInstantBuyPrice($this->oneEuro());
    }

    public function testCannotInstantBuyWithoutInstantBuyOption()
    {
        $auction = new BiddingAndInstantBuyAuction(
            $this->title,
            $this->desc,
            $this->mockInterval(),
            $this->tenEuro(),
            $this->mockUser()
        );

        $this->setExpectedException(
            InvalidArgumentException::class,
            'Cannot instant buy, instant buy price has not been set'
        );
        $auction->instantBuy($this->mockUser());
    }


    public function testCannotInstantBuyAfterAuctionIsWon()
    {
        $auction = new BiddingAndInstantBuyAuction(
            $this->title,
            $this->desc,
            $this->mockInterval(),
            $this->startPrice,
            $this->mockUser()
        );
        $auction->setInstantBuyPrice($this->tenEuro());
        $auction->instantBuy($this->mockUser());

        $this->setExpectedException(
            InvalidArgumentException::class,
            'Auction has already been won'
        );
        $auction->instantBuy($this->mockUser());
    }

    public function testInstantBuyPriceCanBeLowered()
    {
        $seller = $this->mockUser();
        $auction = new BiddingAndInstantBuyAuction(
            $this->title,
            $this->desc,
            $this->mockInterval(),
            $this->startPrice,
            $seller
        );
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
        $auction = new BiddingAndInstantBuyAuction(
            $this->title, $this->desc, $this->mockInterval(), $this->startPrice, $seller);
        $auction->setInstantBuyPrice($this->tenEuro());

        $this->setExpectedException(
            InvalidArgumentException::class,
            'only be changed if new price is lower'
        );
        $auction->setInstantBuyPrice($this->hundredEuro());
    }

    public function testInstantBuyOnlyAfterAuctionStart()
    {
        /** @var PHPUnit_Framework_MockObject_MockObject|AuctionInterval $interval */
        $interval = $this->getMockBuilder('AuctionInterval')
            ->disableOriginalConstructor()
            ->getMock();
        $interval
            ->method('dateIsInInterval')
            ->willReturn(-1);
        $auction = new BiddingAndInstantBuyAuction(
            $this->title,
            $this->desc,
            $interval,
            $this->startPrice,
            $this->mockUser()
        );
        $auction->setInstantBuyPrice($this->tenEuro());

        $this->setExpectedException(
            InvalidArgumentException::class,
            'Auction has not started yet'
        );
        $auction->instantBuy($this->mockUser());
    }

    public function testCannotInstantBuyAfterAuctionClosed()
    {
        $auction = new BiddingAndInstantBuyAuction(
            $this->title,
            $this->desc,
            $this->mockInterval(),
            $this->startPrice,
            $this->mockUser()
        );
        $auction->close();

        $this->setExpectedException(
            InvalidArgumentException::class,
            'Auction has been closed'
        );
        $auction->instantBuy($this->mockUser());
    }
}
