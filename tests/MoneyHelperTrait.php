<?php declare(strict_types = 1);

trait MoneyHelperTrait
{
    private function createMoney()
    {
        return new Money(rand(2, 1000), new Currency('EUR'));
    }

    /**
     * @return Money
     */
    private function oneEuro() : Money
    {
        return new Money(1, new Currency('EUR'));
    }

    /**
     * @return Money
     */
    private function tenEuro() : Money
    {
        return new Money(10, new Currency('EUR'));
    }

    /**
     * @return Money
     */
    private function hundredEuro() : Money
    {
        return new Money(100, new Currency('EUR'));
    }

    /**
     * @return Currency
     */
    private function createUsd()
    {
        $usd = $this->getMockBuilder(Currency::class)
            ->disableOriginalConstructor()
            ->getMock();

        $usd->method('currency')->willReturn('USD');

        return $usd;
    }
}
