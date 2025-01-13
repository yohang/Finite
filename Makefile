build:
	docker build -t yohang/finite .

cli:
	docker run -it --rm -v${PWD}:/app -w/app yohang/finite ash

test:
	docker run -it --rm -v${PWD}:/app -w/app yohang/finite php ./vendor/bin/phpunit --coverage-text

test_all_targets:
	docker build -t yohang/finite:php-8.1 --build-arg PHP_VERSION=8.1 .
	docker build -t yohang/finite:php-8.1-lowest --build-arg PHP_VERSION=8.1 --build-arg DEPENDENCIES=lowest .
	docker build -t yohang/finite:php-8.2 --build-arg PHP_VERSION=8.2 .
	docker build -t yohang/finite:php-8.2-lowest --build-arg PHP_VERSION=8.2 --build-arg DEPENDENCIES=lowest .
	docker build -t yohang/finite:php-8.3 --build-arg PHP_VERSION=8.3 .
	docker build -t yohang/finite:php-8.3-lowest --build-arg PHP_VERSION=8.3 --build-arg DEPENDENCIES=lowest .
	docker build -t yohang/finite:php-8.4 --build-arg PHP_VERSION=8.4 .
	docker build -t yohang/finite:php-8.4-lowest --build-arg PHP_VERSION=8.4 --build-arg DEPENDENCIES=lowest .
	docker run -it --rm yohang/finite:php-8.1 php ./vendor/bin/phpunit --coverage-text
	docker run -it --rm yohang/finite:php-8.1-lowest php ./vendor/bin/phpunit --coverage-text
	docker run -it --rm yohang/finite:php-8.2 php ./vendor/bin/phpunit --coverage-text
	docker run -it --rm yohang/finite:php-8.2-lowest php ./vendor/bin/phpunit --coverage-text
	docker run -it --rm yohang/finite:php-8.3 php ./vendor/bin/phpunit --coverage-text
	docker run -it --rm yohang/finite:php-8.3-lowest php ./vendor/bin/phpunit --coverage-text
	docker run -it --rm yohang/finite:php-8.4 php ./vendor/bin/phpunit --coverage-text
	docker run -it --rm yohang/finite:php-8.4-lowest php ./vendor/bin/phpunit --coverage-text

psalm:
	docker build -t yohang/finite:php-8.4 --build-arg PHP_VERSION=8.4 .
	docker run -it --rm yohang/finite:php-8.4 php ./vendor/bin/psalm --show-info=true
