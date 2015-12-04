<?php declare(strict_types = 1);

class Auction
{
    private $titel;
    private $description;
//    private $startTime;
//    private $endTime;
    private $bids;

    public function __construct(
        AuctionTitle $titel,
        string $description
//        DateTimeImmutable $startTime,
//        DateTimeImmutable $endTime
    ) {
        $this->titel = $titel;
        $this->description = $description;
//        $this->startTime = $startTime;
//        $this->endTime = $endTime;

        $bids = [];
    }

    public function addBidFromUser($bid, $user)
    {
//        $now = DateTimeImmutable::createFromMutable(new DateTime());
//        if ($this->startTime->diff($now)) {
//            throw new InvalidArgumentException('Auction has not started yet.');
//        }
//
//        if ($this->endTime->diff($now)) {
//            throw new InvalidArgumentException('Auction finished.');
//        }

        $this->bids[$bid] = $user;
    }

    public function highestBidder() : string
    {
        $highestBid = max(array_keys($this->bids));
        return $this->bids[$highestBid];
    }
}