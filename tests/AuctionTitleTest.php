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
}
