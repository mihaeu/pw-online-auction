<?php

class Email
{
    private $email;

    public function __construct(string $address)
    {
        if (0 === preg_match('/[\w\d\-]+@\w+\.\w+/', $address)) {
            throw new \InvalidArgumentException($address.' is not valid a valid email.');
        }
        $this->email = $address;
    }

    public function address() : string
    {
        return $this->email;
    }
}