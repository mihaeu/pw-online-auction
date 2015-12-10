<?php declare(strict_types = 1);

/**
 * Helper trait for PHPUnit_Framework_TestCase
 */
trait AuctionHelperTrait
{
    /**
     * @return AuctionInterval|PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockInterval()
    {
        return $this
            ->getMockBuilder('AuctionInterval')
            ->disableOriginalConstructor()
            ->getMock();
    }
}
