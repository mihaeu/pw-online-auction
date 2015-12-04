<?php declare(strict_types = 1);

class Auction
{
    private $title;
    private $description;
    private $startTime;
    private $endTime;
    private $owner;
    private $bids = [];

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
    }

    public function addBidFromUser($bid, $user)
    {
        if ($bid < $this->highestBid()) {
            throw new InvalidArgumentException('Bid must be higher than highest bid '.$this->highestBid());
        }

        if ($user === $this->owner) {
            throw new InvalidArgumentException('Auction owner cannot place bids');
        }

        $this->bids[$bid] = $user;
    }

    public function highestBidder() : string
    {
        if (0 === count($this->bids)) {
            return 0;
        }

        return $this->bids[$this->highestBid()];
    }

    private function highestBid() : int
    {
        if (0 === count($this->bids)) {
            return 0;
        }

        return max(array_keys($this->bids));
    }
}