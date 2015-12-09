<?php declare(strict_types = 1);

/**
 * @covers AuctionInterval
 */
class AuctionIntervalTest extends PHPUnit_Framework_TestCase
{
    public function testStartHasToBeBeforeEnd()
    {
        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/start has to be before end/i');
        new AuctionInterval(new DateTimeImmutable('+2 days'), new DateTimeImmutable());
    }

    public function testMinimumDurationIsOneDay()
    {
        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/minimum duration/i');
        new AuctionInterval(new DateTimeImmutable(), new DateTimeImmutable('+2 hours'));
    }

    public function testDetectsIfTimeInInterval()
    {
        $ai = new AuctionInterval(new DateTimeImmutable(), new DateTimeImmutable('+2 days'));
        $this->assertEquals(0, $ai->dateIsInInterval(new DateTimeImmutable('+1 days')));
    }

    public function testDetectsIfTimeBeforeInterval()
    {
        $ai = new AuctionInterval(new DateTimeImmutable(), new DateTimeImmutable('+2 days'));
        $this->assertEquals(-1, $ai->dateIsInInterval(new DateTimeImmutable('-3 days')));
    }

    public function testDetectsIfTimeAfterInterval()
    {
        $ai = new AuctionInterval(new DateTimeImmutable(), new DateTimeImmutable('+2 days'));
        $this->assertEquals(1, $ai->dateIsInInterval(new DateTimeImmutable('+3 days')));
    }
}
