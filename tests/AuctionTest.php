<?php declare(strict_types = 1);

/**
 * @covers Auction
 * @uses AuctionTitle
 */
class AuctionTest extends PHPUnit_Framework_TestCase
{
    public function testUserCanPlaceBid()
    {
        $auction = new Auction(
            new AuctionTitle('Test'),
            '',
            new DateTimeImmutable(),
            new DateTimeImmutable(),
            ''
        );
        $auction->addBidFromUser(199, 'Mary Doe');
        $auction->addBidFromUser(399, 'John Doe');
        $this->assertEquals('John Doe', $auction->highestBidder());
    }

    public function testOwnerCannotPlaceBids()
    {
        $owner = 'John Doe';
        $auction = new Auction(
            new AuctionTitle('Test'),
            '',
            new DateTimeImmutable(),
            new DateTimeImmutable(),
            $owner
        );

        $this->setExpectedExceptionRegExp(
            InvalidArgumentException::class,
            '/Auction owner cannot place bids/'
        );
        $auction->addBidFromUser(399, $owner);
    }

    public function testBidHasToBeHigherThanPreviouslyHighestBid()
    {
        $auction = new Auction(
            new AuctionTitle('Test'),
            '',
            new DateTimeImmutable(),
            new DateTimeImmutable(),
            ''
        );

        $auction->addBidFromUser(999, '1');
        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/Bid must be higher than highest bid/');
        $auction->addBidFromUser(199, '2');
    }
//
//    public function testCannotBidWhenNotStarted()
//    {
//        $tomorrow = new DateTime();
//        $tomorrow->modify('+1d');
//        $auction = new Auction(
//            new AuctionTitle('Test'),
//            '',
//            DateTimeImmutable::createFromMutable($tomorrow),
//            new DateTimeImmutable(),
//            ''
//        );
//
//        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/Auction has not started yet/');
//        $auction->addBidFromUser(199, '1');
//    }

    public function testFindsHighestBidder()
    {
        $auction = new Auction(
            new AuctionTitle('Test'),
            '',
            new DateTimeImmutable(),
            new DateTimeImmutable(),
            ''
        );

        $this->setExpectedExceptionRegExp(Exception::class, '/No bids/');
        $auction->highestBidder();
    }
}
