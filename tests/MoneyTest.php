<?php declare(strict_types = 1);

namespace Mihaeu\ProductConfigurator;

/**
 * @covers Mihaeu\ProductConfigurator\Money
 * @uses Mihaeu\ProductConfigurator\Currency
 */
class MoneyTest extends \PHPUnit_Framework_TestCase
{
    use CreateMoneyTrait;
    use CreateUsdTrait;

    public function testAmountCanBeRetrieved()
    {
        $money = new Money(1, new Currency('EUR'));

        $this->assertEquals(1, $money->amount());
    }

    public function testCanAddSameCurrencies()
    {
        $money1 = new Money(1, new Currency('EUR'));
        $money2 = new Money(2, new Currency('EUR'));

        $this->assertEquals(3, $money1->addTo($money2)->amount());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Currency mismatch
     */
    public function testWillNotAddDifferentCurrencies()
    {
        $usd = $this->getMockBuilder(Currency::class)
                    ->disableOriginalConstructor()
                    ->getMock();

        $usd->method('currency')->willReturn('USD');

        $money = $this->createMoney();
        $money->addTo(new Money(1, $usd));
    }

    public function testCanCompareSameCurrenciesSameAmount()
    {
        $amount = new Money(1, new Currency('EUR'));

        $this->assertTrue($amount->equals($amount));
    }

    public function testCanCompareSameCurrenciesDifferentAmount()
    {
        $amount1 = new Money(1, new Currency('EUR'));
        $amount2 = new Money(2, new Currency('EUR'));

        $this->assertFalse($amount1->equals($amount2));
    }

    public function testCanCompareDifferentCurrenciesSameAmount()
    {
        $eur = new Money(1, new Currency('EUR'));
        $usd = new Money(1, $this->createUsd());

        $this->assertFalse($eur->equals($usd));
    }

    public function testCanCompareDifferentCurrenciesDifferentAmount()
    {
        $eur = new Money(1, new Currency('EUR'));
        $usd = new Money(2, $this->createUsd());

        $this->assertFalse($eur->equals($usd));
    }

    public function testConvertsToString()
    {
        $this->assertEquals('1EUR', new Money(1, new Currency('EUR')));
    }
}

