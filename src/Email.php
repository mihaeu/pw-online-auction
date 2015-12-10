<?php

class Email
{
    /**
     * @var string
     */
    private $email;

    /**
     * @param string $address
     * @throws InvalidArgumentException
     */
    public function __construct(string $address)
    {
        $this->ensureEmailIsValid($address);

        $this->email = $address;
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return $this->email;
    }

    /**
     * @param string $address
     * @throws InvalidArgumentException
     */
    protected function ensureEmailIsValid(string $address)
    {
        if (0 === preg_match('/[\w\d\-]+@\w+\.\w+/', $address)) {
            throw new \InvalidArgumentException($address . ' is not valid');
        }
    }
}