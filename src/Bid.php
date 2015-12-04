<?php declare(strict_types = 1);

namespace Mihaeu\ProductConfigurator;

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
}