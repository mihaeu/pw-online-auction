<?php declare(strict_types = 1);

trait AuctionHelperTrait
{
    /**
     * @return AuctionInterval|PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockInterval()
    {
        return $this->getMockBuilder('AuctionInterval')->disableOriginalConstructor()->getMock();
    }
}