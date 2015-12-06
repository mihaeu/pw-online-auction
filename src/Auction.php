<?php declare(strict_types = 1);

class Auction
{
    private $title;
    private $description;
    private $interval;
    private $seller;
    private $bids;
    private $startPrice;

    /**
     * @var Money
     */
    private $instantBuyPrice = null;

    /**
     * @var User
     */
    private $winner = null;

    /**
     * @var bool
     */
    private $closed = false;

    /**
     * @param AuctionTitle $title
     * @param AuctionDescription $description
     * @param AuctionInterval $interval
     * @param Money $startPrice
     * @param User $seller
     */
    public function __construct(
        AuctionTitle $title,
        AuctionDescription $description,
        AuctionInterval $interval,
        Money $startPrice,
        User $seller
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->interval = $interval;
        $this->seller = $seller;

        $this->ensureStartPriceIsPositive($startPrice);
        $this->startPrice = $startPrice;

        $this->bids = new BidCollection();
    }

    /**
     * @param Bid $bid
     */
    public function placeBid(Bid $bid)
    {
        $this->ensureAuctionHasStarted();
        $this->ensureAuctionHasNotEnded();
        $this->ensureAuctionHasNotBeenClosed();
        $this->ensureBidIsHigherThanStartPrice($bid);
        $this->ensureNewBidIsHIgherThanLast($bid);
        $this->ensureBidderIsNotSeller($bid->bidder());

        $this->bids->addBid($bid);
    }

    /**
     * @return Bid
     * @throws Exception
     */
    public function highestBid() : Bid
    {
        $highest = $this->bids->findHighest();
        if (null === $highest) {
            throw new Exception('No bids');
        }
        return $highest;
    }

    /**
     * @return User
     */
    public function winner() : User
    {
        return $this->winner;
    }

    /**
     * @param Money $startPrice
     */
    public function setStartPrice(Money $startPrice)
    {
        $this->ensureBiddingHasNotStarted();
        $this->ensureStartPriceIsLowerThanLast($startPrice);

        $this->startPrice = $startPrice;
    }

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
        $this->ensureAuctionHasNotBeenClosed();
        $this->ensureInstantPriceHasBeenSet();
        $this->ensureBidderIsNotSeller($user);

        $this->winner = $user;
    }

    /**
     * @throws InvalidArgumentException
     */
    public function close()
    {
        if ($this->bids->hasBids()) {
            throw new InvalidArgumentException('Cannot close auction after bidding has started');
        }

        $this->closed = true;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function ensureAuctionHasStarted()
    {
        if (AuctionInterval::DATE_BEFORE_INTERVAL === $this->interval->dateIsInInterval(new DateTimeImmutable())) {
            throw new InvalidArgumentException('Auction has not started yet');
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    private function ensureAuctionHasNotEnded()
    {
        if (null !== $this->winner) {
            throw new InvalidArgumentException('Auction has already been won');
        }

        if (AuctionInterval::DATE_AFTER_INTERVAL === $this->interval->dateIsInInterval(new DateTimeImmutable())) {
            throw new InvalidArgumentException('Auction finished');
        }
    }

    /**
     * @param Bid $bid
     * @throws InvalidArgumentException
     */
    private function ensureNewBidIsHIgherThanLast(Bid $bid)
    {
        if ($this->bids->hasBids() && $this->bids->findHighest()->isHigherThan($bid)) {
            throw new InvalidArgumentException('Bid must be higher than highest bid');
        }
    }

    /**
     * @param User $user
     * @throws InvalidArgumentException
     */
    private function ensureBidderIsNotSeller(User $user)
    {
        if ($user->equals($this->seller)) {
            throw new InvalidArgumentException('Seller cannot buy from himself');
        }
    }

    /**
     * @param Money $startPrice
     * @throws InvalidArgumentException
     */
    private function ensureStartPriceIsPositive(Money $startPrice)
    {
        $zero = new Money(0, new Currency('EUR'));
        if ($zero->greaterThan($startPrice)) {
            throw new InvalidArgumentException('Start price has to be positive');
        }
    }

    /**
     * @param Bid $bid
     * @throws InvalidArgumentException
     */
    private function ensureBidIsHigherThanStartPrice(Bid $bid)
    {
        if ($this->startPrice->greaterThan($bid->bid())) {
            throw new InvalidArgumentException('Bid has to be higher than start price');
        }
    }

    /**
     * @throws InvalidArgumentException
     */
    private function ensureAuctionHasNotBeenClosed()
    {
        if (true === $this->closed) {
            throw new InvalidArgumentException('Auction has been closed');
        }
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

    private function ensureInstantPriceHasBeenSet()
    {
        if (null === $this->instantBuyPrice) {
            throw new InvalidArgumentException('Cannot instant buy, instant buy price has not been set');
        }
    }

    private function ensureBiddingHasNotStarted()
    {
        if ($this->bids->hasBids()) {
            throw new InvalidArgumentException('Cannot change start price after bids have been placed');
        }
    }

    /**
     * @param Money $startPrice
     */
    private function ensureStartPriceIsLowerThanLast(Money $startPrice)
    {
        if ($startPrice->greaterThan($this->startPrice)) {
            throw new InvalidArgumentException('Start price can only be lowered');
        }
    }
}