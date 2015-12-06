<?php declare(strict_types = 1);

class AuctionInterval
{
    const MIN_DURATION = 1;
    const DATE_BEFORE_INTERVAL = -1;
    const DATE_AFTER_INTERVAL = 1;
    const DATE_IN_INTERVAL = 0;

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

    /**
     * @param DateTimeImmutable $date
     * @return int -1 if date is before the interval, 0 if it is within or 1 of it is after the interval
     */
    public function dateIsInInterval(DateTimeImmutable $date) : int
    {
        // invert indicates if diff is negative
        if ($this->start->diff($date)->invert === 1) {
            return self::DATE_BEFORE_INTERVAL;
        }

        // invert indicates if diff is negative
        if ($date->diff($this->end)->invert === 1) {
            return self::DATE_AFTER_INTERVAL;
        }

        return self::DATE_IN_INTERVAL;
    }

    /**
     * @param DateTimeImmutable $start
     * @param DateTimeImmutable $end
     *
     * @throws InvalidArgumentException
     */
    private function ensureStartIsBeforeEnd(DateTimeImmutable $start, DateTimeImmutable $end)
    {
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