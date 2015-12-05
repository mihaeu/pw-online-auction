<?php declare(strict_types = 1);

class AuctionDescription
{
    private $description;

    const MIN_LENGTH = 10;

    public function __construct(string $description)
    {
        if (strlen($description) < self::MIN_LENGTH) {
            throw new InvalidArgumentException('Minimum length for description is '.self::MIN_LENGTH);
        }

        $this->description = $description;
    }

    public function __toString() : string
    {
        return $this->description;
    }
}