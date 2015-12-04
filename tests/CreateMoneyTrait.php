<?php declare(strict_types = 1);

trait CreateMoneyTrait
{
    private function createMoney()
    {
        return new Money(rand(2, 1000), new Currency('EUR'));
    }
}
