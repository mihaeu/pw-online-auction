<?php declare(strict_types = 1);

class AuctionDescriptionTest extends PHPUnit_Framework_TestCase
{
    public function testDoesNotAcceptShortDescription()
    {
        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/Minimum length/');
        new AuctionDescription(str_repeat('.', AuctionDescription::MIN_LENGTH - 1));
    }

    public function testAcceptsValidDescription()
    {
        $s = str_repeat('.', AuctionDescription::MIN_LENGTH);
        $desc = new AuctionDescription($s);
        $this->assertEquals($s, $desc);
    }
}
