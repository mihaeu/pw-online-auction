<?php declare(strict_types = 1);

class NicknameTest extends PHPUnit_Framework_TestCase
{
    public function testAcceptsValidNickname()
    {
        $nick = new Nickname('...........');
        $this->assertEquals('...........', $nick);
    }

    public function testRejectsTooShortNickname()
    {
        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/Minimum length/');
        new Nickname('.');
    }

    public function testRejectsTooLongNickname()
    {
        $this->setExpectedExceptionRegExp(InvalidArgumentException::class, '/Maximum length/');
        new Nickname(str_repeat('.', 256));
    }
}
