<?php declare(strict_types = 1);

class AuctionDescription
{
    /**
     * @var string
     */
    private $description;

    const MIN_LENGTH = 10;

    /**
     * @param string $description
     */
    public function __construct(string $description)
    {
        $this->ensureMinLength($description);

        $this->description = $description;
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    private function ensureMinLength(string $description)
    {
        if (strlen($description) < self::MIN_LENGTH) {
            throw new InvalidArgumentException('Minimum length for description is ' . self::MIN_LENGTH);
        }
    }
}