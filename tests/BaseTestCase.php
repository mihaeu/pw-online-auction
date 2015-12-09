<?php declare(strict_types = 1);

class BaseTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @param string|null $address
     * @return PHPUnit_Framework_MockObject_MockObject|User
     */
    protected function mockUser(string $address = null)
    {
        $user = $this->getMockBuilder('User')->disableOriginalConstructor()->getMock();
        if (null !== $address) {
            $email = $this->getMockBuilder('Email')->disableOriginalConstructor()->getMock();
            $email->method('address')->willReturn($address);
            $user->method('email')->willReturn($email);
        }
        return $user;
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|Nickname
     */
    protected function mockNickname()
    {
        return $this->getMockBuilder('Nickname')->disableOriginalConstructor()->getMock();
    }

    /**
     * @param string $emailAddress
     * @return Email|PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockEmail(string $emailAddress = null)
    {
        $email = $this->getMockBuilder('Email')->disableOriginalConstructor()->getMock();
        if (null !== $emailAddress) {
            $email->method('address')->willReturn($emailAddress);
        }
        return $email;
    }

    /**
     * @return Money
     */
    protected function oneEuro() : Money
    {
        return new Money(1, new Currency('EUR'));
    }

    /**
     * @return Money
     */
    protected function tenEuro() : Money
    {
        return new Money(10, new Currency('EUR'));
    }

    /**
     * @return Money
     */
    protected function hundredEuro() : Money
    {
        return new Money(100, new Currency('EUR'));
    }

    /**
     * @return AuctionInterval|PHPUnit_Framework_MockObject_MockObject
     */
    protected function mockInterval()
    {
        return $this->getMockBuilder('AuctionInterval')->disableOriginalConstructor()->getMock();
    }
}
