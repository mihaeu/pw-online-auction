# Online Auction

[![Build Status](https://travis-ci.org/mihaeu/pw-online-auction.svg?branch=develop)](https://travis-ci.org/mihaeu/pw-online-auction)
[![Coverage Status](https://coveralls.io/repos/mihaeu/pw-online-auction/badge.svg?branch=develop&service=github)](https://coveralls.io/github/mihaeu/pw-online-auction?branch=develop)
![PHP v7](https://img.shields.io/badge/PHP-%3E%3D7-blue.svg)

## Getting started

```bash
git clone https://github.com/mihaeu/pw-online-auction
cd pw-online-auction

# for unix users
make testdox

# for mac users
make testdox-osx

# for windows users or people without make
php phpunit.phar -c phpunit.xml.dist --bootstrap tests/bootstrap.php --testdox tests
```

## Requirements (in German by [Stefan Priebsch](https://thephp.cc/company/consultants/stefan-priebsch))

### 1. Online-Auktion

Implementieren Sie die Geschäftslogik für eine Online-Auktion mit den folgenden Geschäftsregeln:

✓ Ein Benutzer hat einen Nicknamen und eine E-Mail-Adresse.

✓ Eine Auktion hat einen Titel, einen Beschreibungstext, einen Start- und Endzeitpunkt sowie einen Startpreis.

✓ Der Startpreis einer Auktion kann nicht mehr verändert werden.

✓ Jeder Benutzer kann Auktionen erstellen.

✓ Abgesehen vom Eigentümer kann jeder Benutzer auf jede Auktion bieten.

✓ Ein Gebot muss immer höher sein als das vorherige Gebot.

✓ Auf eine beendete Auktion kann nicht mehr geboten werden.

### 2. Sofortkauf-Feature für die Online-Auktion

Erweiern Sie die Online-Auktion um ein "Sofortkauf"-Feature. 

✓ Für jede Auktion kann dieses Feature optional aktiviert werden, so lange noch kein Gebot abgegeben wurde.

✓ Ist das Feature für eine Auktion einmal aktiviert, kann es nicht mehr abgeschaltet werden.

✓ Für den Sofortkauf muss ein Preis angegeben werden, der höher ist als der Startpreis.

✓ Der Sofortkauf-Preis kann nicht geändert werden.

### 3. Veränderte Geschäftsregeln

Ändern Sie die folgenden Geschäftsregeln der Online-Auktion: 

✓ Der Sofortkauf-Preis kann nach unten geändert werden, solange er das aktuelle Gebot nicht unterschreitet.

✓ Der Startpreis einer Auktion kann nur nach unten geändert werden, so lange noch keine Gebote abgegeben wurden.

✓ Eine Auktion kann vom Eigentümer vorzeitig beendet werden, so lange es noch keine Gebote gibt.

### Zur Vorgehensweise:

Schreiben Sie (mit Ausnahme einer Bootstrap-Datei) ausschließich
objektorientierten Code. Benutzen Sie einen Test-First-Ansatz, idealerweise
entwickeln Sie testgetrieben. 

Erzielen Sie 100% Code Coverage mit der strikten, in der Vorlesung eingeführten
Konfiguration von PHPUnit sowie @covers-Annotationen für jede Testklasse.

Benennen Sie die Testmethoden so, dass die Ausgabe von PHPUnit mit dem Schalter
--testdox eine lesbare und ausführbare Spezifikation der Geschäftsregeln bildet.

Schreiben Sie keinen Code für die Persistenz und keinen Code für die
Präsentation. Verwenden Sie keine Frameworks oder Bibliotheken.

Es kommt nicht darauf an, dass Sie alle Features implementieren. Sie dürfen
allerdings keine unfertigen oder ungetesteten Features abliefern. Erwägen Sie,
eine Versionskontrolle wie Git zu verwenden, damit Sie einfach zum letzten 
funktionierenden Stand der Software zurückkehren können.

Fokussieren Sie auf Funktionalitäten mit höherem Geschäftswert und
implementieren Sie keine zusätzlichen Features, die nicht in der
Aufgabenstellung verlangt wurden.

Arbeiten Sie die Aufgaben auf jeden Fall in der gegebenen Reihenfolge ab und 
beginnen Sie die nächste Aufgabe erst dann, wenn Sie die vorherige Aufgabe
vollständig abgeschlossen haben.

## Questions

 - When using asserts in tests is it better to use hard coded values or constants exported by the class?
 - Should exception messages (magic strings) be constants?
 - `BiddingAuction::testReturnsWinnerAfterAuctionEnd` which testing approach is better?
 ```php
public function testReturnsWinnerAfterAuctionEnd()
    {
        //-------------------------------------
        // Approach A: easy to understand and better unit test
        //-------------------------------------

        // inject so that bids have already been placed
        $bids = new BidCollection();
        $bids->addBid(new Bid($this->tenEuro(), $this->mockUser()));

        $highestBidder = $this->mockUser();
        $bids->addBid(new Bid($this->hundredEuro(), $highestBidder));

        // mock: Auction finished
        $interval = $this->mockInterval();
        $interval->method('dateIsInInterval')->willReturn(1);

        $auction = new BiddingAuction(
            $this->title,
            $this->desc,
            $interval,
            $this->startPrice,
            $this->mockUser(),
            $bids
        );
        $this->assertEquals($highestBidder, $auction->winner());

        //-------------------------------------
        // Approach B: documents bidding process, but hard to understand
        //-------------------------------------

        // we have to mock the AuctionInterval in order to simulate the
        // time frame during and after the auction without slowing down tests
        $interval = $this->mockInterval();
        $interval->method('dateIsInInterval')->will($this->onConsecutiveCalls(
            0, // 1st bid start time check
            0, // 1st bid end time check
            0, // 2nd bid start time check
            0, // 2nd bid end time check
            1  // auction finished when checking for winner
        ));
        $auction = new BiddingAuction(
            $this->title,
            $this->desc,
            $interval,
            $this->startPrice,
            $this->mockUser()
        );

        $highestBidder = $this->mockUser('mail@highest.com');
        $auction->placeBid(new Bid($this->tenEuro(), $this->mockUser()));
        $auction->placeBid(new Bid($this->hundredEuro(), $highestBidder));
        $this->assertEquals($highestBidder, $auction->winner());

        return $auction;
    }
 ```
 
 (*) assuming InvalidArgumentException fits the case
 
## Testdox

```bash
AuctionDescription
 [x] Does not accept short description
 [x] Accepts valid description

AuctionInterval
 [x] Start has to be before end
 [x] Minimum duration is one day
 [x] Detects if time in interval
 [x] Detects if time before interval
 [x] Detects if time after interval

AuctionTitle
 [x] Prints title
 [x] Rejects short title
 [x] Rejects long title

BidCollection
 [x] Highest bid without bids is 0
 [x] One bid is highest bid
 [x] Find highest bid
 [x] Detects when empty
 [x] Detects when not empty

Bid
 [x] Returns bid
 [x] Returns user
 [x] Compares bids
 [x] Bid is positive

BiddingAndInstantBuyAuction
 [x] Can activate instant buy
 [x] Seller cannot instant buy
 [x] Cannot set instant buy lower than highest bid
 [x] Instant buy price has to be higher than start price
 [x] Cannot instant buy without instant buy option
 [x] Cannot instant buy after auction is won
 [x] Instant buy price can be lowered
 [x] Instant buy price cannot be increased
 [x] Instant buy only after auction start
 [x] Cannot instant buy after auction closed

BiddingAuction
 [x] User can place bid
 [x] Owner cannot place bids
 [x] Bid has to be higher than previously highest bid
 [x] Finds highest bidder
 [x] Cannot bid before auction start
 [x] Cannot bid after auction
 [x] Start price has to be positive
 [x] Bid has to be higher than start price
 [x] Can change start price before bids have been placed
 [x] Cannot change start price after bids have been placed
 [x] Start price can only be lowered
 [x] Cannot close after bidding has started
 [x] Cannot bid after auction closed
 [x] Cannot bid after auction is won
 [x] Returns winner after auction end
 [x] Cannot place bid after auction has been won

Currency
 [x] Supports eur
 [x] Does not support non eur currency
 [x] Currency can be retrieved
 [x] Can compare same currencies
 [x] Can compare different currencies
 [x] Can compare currencies not equal
 [x] Converts to string

Email
 [x] Accepts valid email
 [x] Does not accept invalid email

Money
 [x] Amount can be retrieved
 [x] Can add same currencies
 [x] Will not add different currencies
 [x] Can compare same currencies same amount
 [x] Can compare same currencies different amount
 [x] Can compare different currencies same amount
 [x] Can compare different currencies different amount
 [x] Converts to string
 [x] Compares amounts

Nickname
 [x] Accepts valid nickname
 [x] Rejects too short nickname
 [x] Rejects too long nickname

User
 [x] Users with same email are equal
 [x] Users with different emails are not equal
```
