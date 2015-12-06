# Online Auction

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

## Requirements (by Stefan Priebsch)

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
