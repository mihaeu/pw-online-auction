<?php declare(strict_types = 1);

class AuctionTitle
{
    const MIN_LENGTH = 3;
    const MAX_LENGTH = 255;
    /**
     * @var string
     */
    private $title;

    public function __construct(string $title)
    {
        if (strlen($title) < self::MIN_LENGTH) {
            throw new InvalidArgumentException('Min length is '.self::MIN_LENGTH);
        }

        if (strlen($title) > self::MAX_LENGTH) {
            throw new InvalidArgumentException('Max length is '.self::MAX_LENGTH);
        }

        $this->title = $title;
    }

    public function __toString() : string
    {
        return $this->title;
    }
}