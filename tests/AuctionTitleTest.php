<?php declare(strict_types = 1);

/**
 * @covers AuctionTitle
 */
class AuctionTitleTest extends PHPUnit_Framework_TestCase
{
    public function testPrintsTitle()
    {
        $title = new AuctionTitle('test');
        $this->assertEquals('test', $title->__toString());
    }

    public function testRejectsShortTitle()
    {
        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/Min length is \d+/');
        new AuctionTitle(str_repeat('.', AuctionTitle::MIN_LENGTH - 1));
    }

    public function testRejectsLongTitle()
    {
        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/Max length is \d+/');
        new AuctionTitle(str_repeat('.', AuctionTitle::MAX_LENGTH + 1));
    }
}
