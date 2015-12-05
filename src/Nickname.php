<?php declare(strict_types = 1);

class Nickname
{
    const MIN_LENGTH = 5;
    const MAX_LENGTH = 255;
    /**
     * @var string
     */
    private $nickname;

    /**
     * @param $nickname
     */
    public function __construct(string $nickname)
    {
        if (strlen($nickname) < self::MIN_LENGTH) {
            throw new InvalidArgumentException('Minimum length is '.self::MIN_LENGTH);
        }

        if (strlen($nickname) > self::MAX_LENGTH) {
            throw new InvalidArgumentException('Maximum length is '.self::MAX_LENGTH);
        }

        $this->nickname = $nickname;
    }

    public function __toString() : string
    {
        return $this->nickname;
    }
}