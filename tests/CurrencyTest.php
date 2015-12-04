<?php declare(strict_types = 1);

/**
 * @covers Currency
 */
class CurrencyTest extends \PHPUnit_Framework_TestCase
{
    use CreateUsdTrait;

    public function testSupportsEur()
    {
        $this->assertInstanceOf(Currency::class, new Currency('EUR'));
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Unsupported currency
     */
    public function testDoesNotSupportNonEurCurrency()
    {
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

    public function testConvertsToString()
    {
        $this->assertEquals('EUR', new Currency('EUR'));
    }
}
