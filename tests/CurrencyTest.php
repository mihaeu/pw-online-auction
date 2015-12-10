<?php declare(strict_types = 1);

/**
 * @covers Currency
 */
class CurrencyTest extends \PHPUnit_Framework_TestCase
{
    use MoneyHelperTrait;

    public function testSupportsEur()
    {
        $this->assertInstanceOf(Currency::class, new Currency('EUR'));
    }

    public function testDoesNotSupportNonEurCurrency()
    {
        $this->setExpectedException(
            InvalidArgumentException::class,
            'Unsupported currency'
        );
        new Currency('no-EUR');
    }

    public function testCurrencyCanBeRetrieved()
    {
        $currency = 'EUR';

        $object = new Currency($currency);

        $this->assertEquals($currency, $object->currency());
    }

    public function testCanCompareSameCurrencies()
    {
        $currency = new Currency('EUR');

        $this->assertTrue($currency->equals($currency));
    }

    public function testCanCompareDifferentCurrencies()
    {
        $eur = new Currency('EUR');

        $this->assertFalse($eur->equals($this->createUsd()));
    }

    public function testCanCompareCurrenciesNotEqual()
    {
        $eur = new Currency('EUR');

        $this->assertTrue($eur->notEquals($this->createUsd()));
    }

    public function testConvertsToString()
    {
        $this->assertEquals('EUR', new Currency('EUR'));
    }
}
