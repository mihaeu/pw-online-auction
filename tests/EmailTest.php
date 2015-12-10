<?php

/**
 * @coversDefaultClass Email
 */
class EmailTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::__toString
     * @covers ::ensureEmailIsValid
     */
    public function testAcceptsValidEmail()
    {
        $email = new Email('me@email.com');
        $this->assertEquals('me@email.com', $email);
    }

    /**
     * @covers ::__construct
     * @covers ::ensureEmailIsValid
     */
    public function testDoesNotAcceptInvalidEmail()
    {
        $this->setExpectedException('InvalidArgumentException', 'is not valid');
        new Email('bademail');
    }
}
