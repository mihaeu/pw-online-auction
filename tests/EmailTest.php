<?php

/**
 * @covers Email
 */
class EmailTest extends \PHPUnit_Framework_TestCase
{
    public function testAcceptsValidEmail()
    {
        $email = new Email('me@email.com');
        $this->assertEquals('me@email.com', $email->address());
    }

    public function testDoesNotAcceptInvalidEmail()
    {
        $this->setExpectedException('InvalidArgumentException');
        new Email('bademail');
    }
}
