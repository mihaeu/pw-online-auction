<?php declare(strict_types = 1);

/**
 * @covers User
 */
class UserTest extends PHPUnit_Framework_TestCase
{
    public function testUsersWithSameEmailAreEqual()
    {
        $user1 = new User($this->mockNickname(), $this->mockEmail('some@email.com'));
        $user2 = new User($this->mockNickname(), $this->mockEmail('some@email.com'));
        $this->assertTrue($user1->equals($user2));
    }

    public function testUsersWithDifferentEmailsAreNotEqual()
    {
        $user1 = new User($this->mockNickname(), $this->mockEmail('one@email.com'));
        $user2 = new User($this->mockNickname(), $this->mockEmail('other@email.com'));
        $this->assertFalse($user1->equals($user2));
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|Nickname
     */
    private function mockNickname()
    {
        return $this->getMockBuilder('Nickname')->disableOriginalConstructor()->getMock();
    }

    /**
     * @return PHPUnit_Framework_MockObject_MockObject|Email
     */
    private function mockEmail(string $address = null)
    {
        $email = $this->getMockBuilder('Email')->disableOriginalConstructor()->getMock();
        if (null !== $address) {
            $email->method('address')->willReturn($address);
        }
        return $email;
    }
}
