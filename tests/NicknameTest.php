<?php declare(strict_types = 1);

/**
 * @covers Nickname
 */
class NicknameTest extends PHPUnit_Framework_TestCase
{
    public function testAcceptsValidNickname()
    {
        $nick = new Nickname('...........');
        $this->assertEquals('...........', $nick);
    }

    public function testRejectsTooShortNickname()
    {
        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/Minimum length is \d+/');
        new Nickname('.');
    }

    public function testRejectsTooLongNickname()
    {
        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/Maximum length is \d+/');
        new Nickname(str_repeat('.', 256));
    }
}
