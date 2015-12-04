<?php declare(strict_types = 1);

class AuctionTitle
{
    /**
     * @var string
     */
    private $title;

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    public function __toString() : string
    {
        return $this->title;
    }
}