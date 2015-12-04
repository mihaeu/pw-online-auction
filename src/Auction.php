<?php declare(strict_types = 1);

class Auction
{
    private $title;
    private $description;
    private $startTime;
    private $endTime;
    private $owner;
    private $bids;

    public function __construct(
        AuctionTitle $title,
        AuctionDescription $description,
        DateTimeImmutable $startTime,
        DateTimeImmutable $endTime,
        string $owner
    ) {
        $this->title = $title;
        $this->description = $description;
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->owner = $owner;

        $this->bids = new BidCollection();
    }

    public function addBidFromUser(Bid $bid)
    {
        $this->ensureAuctionHasStarted();
        $this->ensureAuctionHasNotEnded();
        $this->ensureNewBidIsHIgherThanLast($bid);
        $this->ensureBidderIsNotSeller($bid);

        $this->bids->addBid($bid);
    }

    public function highestBid() : Bid
    {
        $highest = $this->bids->findHighest();
        if (null === $highest) {
            throw new Exception('No bids');
        }
        return $highest;
    }

    private function ensureAuctionHasStarted()
    {
        $now = new DateTimeImmutable();

        // the invert flag of DateTimeImmutable is set to 1 if the difference is negative
        if (1 === $this->startTime->diff($now)->invert) {
            throw new InvalidArgumentException('Auction has not started yet');
        }
    }

    private function ensureAuctionHasNotEnded()
    {
        $now = new DateTimeImmutable();

        // the invert flag of DateTimeImmutable is set to 1 if the difference is negative
        if (1 === $now->diff($this->endTime)->invert) {
            throw new InvalidArgumentException('Auction finished');
        }
    }

    /**
     * @param Bid $bid
     */
    private function ensureNewBidIsHIgherThanLast(Bid $bid)
    {
        if ($this->bids->hasBids() && $this->bids->findHighest()->isHigherThan($bid)) {
            throw new InvalidArgumentException('Bid must be higher than highest bid');
        }
    }

    /**
     * @param Bid $bid
     */
    private function ensureBidderIsNotSeller(Bid $bid)
    {
        if ($bid->user() === $this->owner) {
            throw new InvalidArgumentException('Auction owner cannot place bids');
        }
    }
}