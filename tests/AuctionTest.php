<?php declare(strict_types = 1);

/**
 * @covers Auction
 */
class AuctionTest extends PHPUnit_Framework_TestCase
{
    public function testUserCanPlaceBid()
    {
        $auction = new Auction(new AuctionTitle('Test'),'');
        $auction->addBidFromUser(3.99, 'John Doe');
        $auction->addBidFromUser(1.99, 'Mary Doe');
        $this->assertEquals('John Doe', $auction->highestBidder());
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
