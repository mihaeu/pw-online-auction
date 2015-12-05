<?php declare(strict_types = 1);

/**
 * @covers User
 */
class UserTest extends PHPUnit_Framework_TestCase
{
    public function testUsersWithSameEmailAreEqual()
    {
        $nickname = $this->getMockBuilder('Nickname')->disableOriginalConstructor()->getMock();
        $email1 = $this->getMockBuilder('Email')->disableOriginalConstructor()->getMock();
        $email1->method('address')->willReturn('one@email.com');
        $user1 = new User($nickname, $email1);
        $user2 = new User($nickname, $email1);
        $this->assertTrue($user1->equals($user2));
    }

    public function testUsersWithDifferentEmailsAreNotEqual()
    {
        $nickname = $this->getMockBuilder('Nickname')->disableOriginalConstructor()->getMock();
        $email1 = $this->getMockBuilder('Email')->disableOriginalConstructor()->getMock();
        $email1->method('address')->willReturn('one@email.com');
        $email2 = $this->getMockBuilder('Email')->disableOriginalConstructor()->getMock();
        $email2->method('address')->willReturn('other@email.com');
        $user1 = new User($nickname, $email1);
        $user2 = new User($nickname, $email2);
        $this->assertFalse($user1->equals($user2));
    }
}
