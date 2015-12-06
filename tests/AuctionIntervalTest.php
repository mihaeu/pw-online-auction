<?php declare(strict_types = 1);

class AuctionIntervalTest extends BaseTestCase
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
        $this->assertTrue($ai->dateIsInInterval(new DateTimeImmutable('+1 days')));
    }

    public function testDetectsIfTimeOutsideInterval()
    {
        $ai = new AuctionInterval(new DateTimeImmutable(), new DateTimeImmutable('+2 days'));
        $this->assertFalse($ai->dateIsInInterval(new DateTimeImmutable('+3 days')));
    }
}
