<?php declare(strict_types = 1);

class User
{
    private $nickname;
    private $email;

    public function __construct(Nickname $nickname, Email $email)
    {
        $this->nickname = $nickname;
        $this->email = $email;
    }

    public function equals(User $user2) : bool
    {
        return $this->email->address() === $user2->email()->address();
    }

    public function email() : Email
    {
        return $this->email;
    }
}
