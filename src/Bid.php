<?php declare(strict_types = 1);

class Bid
{
    /**
     * @var Money
     */
    private $bid;

    /**
     * @var string
     */
    private $user;

    /**
     * Bid constructor.
     * @param Money $bid
     * @param string $user
     */
    public function __construct(Money $bid, string $user)
    {
        $this->bid = $bid;
        $this->user = $user;
    }

    public function bid() : Money
    {
        return $this->bid;
    }

    public function user() : string
    {
        return $this->user;
    }

    public function isHigherThan(Bid $bid2) : bool
    {
        return $this->bid->amount() > $bid2->bid()->amount();
    }
}