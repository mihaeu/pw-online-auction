<?php declare(strict_types = 1);

class Bid
{
    /**
     * @var Money
     */
    private $bid;

    /**
     * @var User
     */
    private $bidder;

    /**
     * Bid constructor.
     * @param Money $bid
     * @param User $bidder
     */
    public function __construct(Money $bid, User $bidder)
    {
        $this->ensureBidIsPositive($bid);

        $this->bid = $bid;
        $this->bidder = $bidder;
    }

    /**
     * @return Money
     */
    public function bid() : Money
    {
        return $this->bid;
    }

    /**
     * @return User
     */
    public function bidder() : User
    {
        return $this->bidder;
    }

    /**
     * @param Bid $bid2
     * @return bool
     */
    public function isHigherThan(Bid $bid2) : bool
    {
        return $this->bid->amount() > $bid2->bid()->amount();
    }

    /**
     * @param Money $bid
     */
    private function ensureBidIsPositive(Money $bid)
    {
        $zero = new Money(0, new Currency('EUR'));
        if (!$bid->greaterThan($zero)) {
            throw new InvalidArgumentException('Bid must be higher than 0');
        }
    }
}