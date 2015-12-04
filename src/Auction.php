<?php declare(strict_types = 1);

class Auction
{
    private $title;
    private $description;
    private $startTime;
    private $endTime;
    private $owner;

    /**
     * @var BidCollection
     */
    private $bids;

    public function __construct(
        AuctionTitle $title,
        string $description,
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
        if ($this->bids->hasBids() && $this->bids->findHighest()->isHigherThan($bid)) {
            throw new InvalidArgumentException('Bid must be higher than highest bid');
        }

        if ($bid->user() === $this->owner) {
            throw new InvalidArgumentException('Auction owner cannot place bids');
        }

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
}