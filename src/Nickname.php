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
     * @throws InvalidArgumentException
     */
    public function __construct(string $nickname)
    {
        $this->ensureNicknameIsNotTooShort($nickname);
        $this->ensureNicknameIsNotTooLong($nickname);

        $this->nickname = $nickname;
    }

    /**
     * @return string
     */
    public function __toString() : string
    {
        return $this->nickname;
    }

    /**
     * @param string $nickname
     * @throws InvalidArgumentException
     */
    private function ensureNicknameIsNotTooShort(string $nickname)
    {
        if (strlen($nickname) < self::MIN_LENGTH) {
            throw new InvalidArgumentException('Minimum length is ' . self::MIN_LENGTH);
        }
    }

    /**
     * @param string $nickname
     * @throws InvalidArgumentException
     */
    private function ensureNicknameIsNotTooLong(string $nickname)
    {
        if (strlen($nickname) > self::MAX_LENGTH) {
            throw new InvalidArgumentException('Maximum length is ' . self::MAX_LENGTH);
        }
    }
}