<?php declare(strict_types = 1);

/**
 * @coversDefaultClass AuctionDescription
 */
class AuctionDescriptionTest extends PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     */
    public function testDoesNotAcceptShortDescription()
    {
        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/Minimum length/');
        new AuctionDescription(str_repeat('.', AuctionDescription::MIN_LENGTH - 1));
    }

    /**
     * @covers ::__construct
     * @covers ::__toString
     */
    public function testAcceptsValidDescription()
    {
        $s = str_repeat('.', AuctionDescription::MIN_LENGTH);
        $desc = new AuctionDescription($s);
        $this->assertEquals($s, $desc);
    }
}
