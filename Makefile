NO_COLOR=\x1b[0m
OK_COLOR=\x1b[32;01m
ERROR_COLOR=\x1b[31;01m
WARN_COLOR=\x1b[33;01m

phpab:
	php phpab.phar -o src/autoload.php src
	php phpab.phar -o tests/autoload.php tests

test:
	php phpunit.phar -c phpunit.xml.dist --bootstrap tests/bootstrap.php tests

testdox:
	@php phpunit.phar -c phpunit.xml.dist --bootstrap tests/bootstrap.php --testdox tests \
	 | sed 's/\[x\]/$(OK_COLOR)$\[x]$(NO_COLOR)/' \
	 | sed -r 's/(\[ \].+)/$(ERROR_COLOR)\1$(NO_COLOR)/' \
	 | sed -r 's/(^[^ ].+)/$(WARN_COLOR)\1$(NO_COLOR)/'

testdox-osx:
	@php phpunit.phar -c phpunit.xml.dist --bootstrap tests/bootstrap.php --testdox tests \
	 | sed 's/\[x\]/$(OK_COLOR)$\[x]$(NO_COLOR)/' \
	 | sed -E 's/(\[ \].+)/$(ERROR_COLOR)\1$(NO_COLOR)/' \
	 | sed -E 's/(^[^ ].+)/$(WARN_COLOR)\1$(NO_COLOR)/'

cov:
	@php phpunit.phar -c phpunit.xml.dist --bootstrap tests/bootstrap.php --coverage-text