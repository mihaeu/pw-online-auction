<?php declare(strict_types = 1);

trait UserHelperTrait
{
    /**
     * @param string|null $emailAddress
     * @return PHPUnit_Framework_MockObject_MockObject|User
     */
    protected function mockUser(string $emailAddress = null)
    {
        $user = $this->getMockBuilder('User')->disableOriginalConstructor()->getMock();
        if (null !== $emailAddress) {
            $email = $this->getMockBuilder('Email')->disableOriginalConstructor()->getMock();
            $email->method('__toString')->willReturn($emailAddress);
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
            $email->method('__toString')->willReturn($emailAddress);
        }
        return $email;
    }
}