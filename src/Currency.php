<?php declare(strict_types = 1);

class Currency
{
    /**
     * @var string
     */
    private $currency;

    /**
     * @var string[]
     */
    private $supportedCurrencies = ['EUR'];

    /**
     * @param string $currency
     */
    public function __construct(string $currency)
    {
        $this->ensureCurrencyIsSupported($currency);

        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function currency() :string
    {
        return $this->currency;
    }

    /**
     * @param Currency $currency
     *
     * @return bool
     */
    public function equals(Currency $currency) :bool
    {
        return $this->currency === $currency->currency();
    }

    /**
     * @param $currency
     */
    private function ensureCurrencyIsSupported($currency)
    {
        if (!in_array($currency, $this->supportedCurrencies)) {
            throw new \InvalidArgumentException('Unsupported currency');
        }
    }

    public function __toString() : string
    {
        return $this->currency;
    }
}
