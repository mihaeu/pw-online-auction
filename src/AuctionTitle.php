<?php declare(strict_types = 1);

class AuctionTitle
{
    const MIN_LENGTH = 3;
    const MAX_LENGTH = 255;

    /**
     * @var string
     */
    private $title;

    /**
     * @param string $title
     * @throws InvalidArgumentException
     */
    public function __construct(string $title)
    {
        $this->ensureMinLength($title);
        $this->ensureMaxLength($title);

        $this->title = $title;
    }

    public function __toString() : string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    protected function ensureMinLength(string $title)
    {
        if (strlen($title) < self::MIN_LENGTH) {
            throw new InvalidArgumentException('Min length is ' . self::MIN_LENGTH);
        }
    }

    /**
     * @param string $title
     */
    protected function ensureMaxLength(string $title)
    {
        if (strlen($title) > self::MAX_LENGTH) {
            throw new InvalidArgumentException('Max length is ' . self::MAX_LENGTH);
        }
    }
}