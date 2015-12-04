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
        $auction->addBidFromUser(1.99, 'Mary Doe');
        $auction->addBidFromUser(3.99, 'John Doe');
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
        $auction->addBidFromUser(3.99, $owner);
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

        $auction->addBidFromUser(9.99, '1');
        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/Bid must be higher than highest bid/');
        $auction->addBidFromUser(1.99, '2');
    }

//    public function testCannotBidWhenNotStarted()
//    {
//        $auction = new Auction(new AuctionTitle('test'), '');
//
////        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '//');
////        $auction->place('');
//    }
//
//    public function testCannotBidWhenFinished()
//    {
//        $auction = new Auction(new AuctionTitle('test'), '');
//
////        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '//');
////        $auction->bid('');
//    }
}
