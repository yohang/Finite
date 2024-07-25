build:
	docker build -t yohang/finite .

cli:
	docker run -it --rm -v${PWD}:/app -w/app yohang/finite ash

test:
	docker run -it --rm -v${PWD}:/app -w/app yohang/finite php ./vendor/bin/phpunit

test_all_targets:
	docker build -t yohang/finite:php-8.1 --build-arg PHP_VERSION=8.1 .
	docker build -t yohang/finite:php-8.1-lowest --build-arg PHP_VERSION=8.1 --build-arg DEPENDENCIES=lowest .
	docker build -t yohang/finite:php-8.2 --build-arg PHP_VERSION=8.2 .
	docker build -t yohang/finite:php-8.2-lowest --build-arg PHP_VERSION=8.2 --build-arg DEPENDENCIES=lowest .
	docker build -t yohang/finite:php-8.3 --build-arg PHP_VERSION=8.3 .
	docker build -t yohang/finite:php-8.3-lowest --build-arg PHP_VERSION=8.3 --build-arg DEPENDENCIES=lowest .
	docker run -it --rm yohang/finite:php-8.1 php ./vendor/bin/phpunit
	docker run -it --rm yohang/finite:php-8.1-lowest php ./vendor/bin/phpunit
	docker run -it --rm yohang/finite:php-8.2 php ./vendor/bin/phpunit
	docker run -it --rm yohang/finite:php-8.2-lowest php ./vendor/bin/phpunit
	docker run -it --rm yohang/finite:php-8.3 php ./vendor/bin/phpunit
	docker run -it --rm yohang/finite:php-8.3-lowest php ./vendor/bin/phpunit
