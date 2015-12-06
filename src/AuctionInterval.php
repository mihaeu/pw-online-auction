<?php declare(strict_types = 1);

class AuctionInterval
{
    const MIN_DURATION = 1;

    private $start;
    private $end;

    /**
     * @param DateTimeImmutable $start
     * @param DateTimeImmutable $end
     */
    public function __construct(DateTimeImmutable $start, DateTimeImmutable $end)
    {
        $this->ensureStartIsBeforeEnd($start, $end);
        $this->ensureDurationIsAtLeastOneDay($start, $end);

        $this->start = $start;
        $this->end = $end;
    }

    public function dateIsInInterval(DateTimeImmutable $date)
    {
        return $this->start->diff($date)->invert === 0 && $date->diff($this->end)->invert === 0;
    }

    /**
     * @param DateTimeImmutable $start
     * @param DateTimeImmutable $end
     *
     * @throws InvalidArgumentException
     */
    private function ensureStartIsBeforeEnd(DateTimeImmutable $start, DateTimeImmutable $end)
    {
        $diff = $start->diff($end);
        // the invert flag indicates that the diff is negative
        if (1 === $start->diff($end)->invert) {
            throw new InvalidArgumentException('Start has to be before end');
        }
    }

    /**
     * @param DateTimeImmutable $start
     * @param DateTimeImmutable $end
     *
     * @throws InvalidArgumentException
     */
    private function ensureDurationIsAtLeastOneDay(DateTimeImmutable $start, DateTimeImmutable $end)
    {
        if ($start->diff($end)->d < self::MIN_DURATION) {
            throw new InvalidArgumentException('Minimum duration is '.self::MIN_DURATION);
        }
    }
}