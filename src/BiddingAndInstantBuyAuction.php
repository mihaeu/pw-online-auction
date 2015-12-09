<?php declare(strict_types = 1);

class BiddingAndInstantBuyAuction extends BiddingAuction
{
    /**
     * @var Money
     */
    private $instantBuyPrice = null;

    /**
     * @param Money $instantBuyPrice
     * @throws InvalidArgumentException
     */
    public function setInstantBuyPrice(Money $instantBuyPrice)
    {
        $this->ensureInstantBuyIsHigherThanLastBid($instantBuyPrice);
        $this->ensureInstantBuyPriceIsHigherThanStartPrice($instantBuyPrice);
        $this->ensureInstantPriceCanOnlyBeLowered($instantBuyPrice);

        $this->instantBuyPrice = $instantBuyPrice;
    }

    /**
     * @param User $user
     * @throws InvalidArgumentException
     */
    public function instantBuy(User $user)
    {
        $this->ensureAuctionHasStarted();
        $this->ensureAuctionHasNotEnded();
        $this->ensureInstantPriceHasBeenSet();
        $this->ensureBidderIsNotSeller($user);

        $this->winner = $user;
    }

    /**
     * @param Money $instantBuyPrice
     * @throws Exception
     */
    private function ensureInstantBuyIsHigherThanLastBid(Money $instantBuyPrice)
    {
        if ($this->bids->hasBids() && $this->highestBid()->bid()->greaterThan($instantBuyPrice)) {
            throw new InvalidArgumentException('Instant buy has to be higher than highest bid');
        }
    }

    /**
     * @param Money $instantBuyPrice
     */
    private function ensureInstantBuyPriceIsHigherThanStartPrice(Money $instantBuyPrice)
    {
        if ($this->startPrice->greaterThan($instantBuyPrice)) {
            throw new InvalidArgumentException('Instant buy price has to be higher than start price');
        }
    }

    /**
     * @param Money $instantBuyPrice
     */
    private function ensureInstantPriceCanOnlyBeLowered(Money $instantBuyPrice)
    {
        if (null !== $this->instantBuyPrice && $instantBuyPrice->greaterThan($this->instantBuyPrice)) {
            throw new InvalidArgumentException('Instant buy price can only be changed if new price is lower');
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    private function ensureInstantPriceHasBeenSet()
    {
        if (null === $this->instantBuyPrice) {
            throw new InvalidArgumentException('Cannot instant buy, instant buy price has not been set');
        }
    }
}