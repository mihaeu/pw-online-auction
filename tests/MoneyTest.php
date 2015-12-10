<?php declare(strict_types = 1);

/**
 * @covers Money
 * @uses Currency
 */
class MoneyTest extends \PHPUnit_Framework_TestCase
{
    use MoneyHelperTrait;

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

    public function testWillNotAddDifferentCurrencies()
    {
        /** @var PHPUnit_Framework_MockObject_MockObject|Currency $usd */
        $usd = $this->getMockBuilder(Currency::class)
                    ->disableOriginalConstructor()
                    ->getMock();

        $usd->method('currency')->willReturn('USD');

        $money = $this->createMoney();

        $this->setExpectedException(InvalidArgumentException::class, 'Currency mismatch');
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

    public function testComparesAmounts()
    {
        $eur1 = new Money(1, new Currency('EUR'));
        $eur2 = new Money(2, new Currency('EUR'));
        $this->assertTrue($eur2->greaterThan($eur1));
    }
}

