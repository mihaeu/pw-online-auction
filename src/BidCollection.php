<?php declare(strict_types = 1);

class BidCollection
{
    /**
     * @var Bid[]
     */
    private $bids;

    /**
     * @param Bid $bid
     */
    public function addBid(Bid $bid)
    {
        $this->bids[] = $bid;
    }

    /**
     * @return Bid|null
     */
    public function findHighest()
    {
        if (0 === count($this->bids)) {
            return null;
        }

        $max = $this->bids[0];
        foreach ($this->bids as $bid) {
            if ($max->bid()->amount() < $bid->bid()->amount()) {
                $max = $bid;
            }
        }
        return $max;
    }

    /**
     * @return bool
     */
    public function hasBids() : bool
    {
        return 0 !== count($this->bids);
    }
}
