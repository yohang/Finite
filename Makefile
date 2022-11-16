build:
	docker build -t yohang/finite .

cli:
	docker run -it --rm -v${PWD}:/app -w/app yohang/finite ash

test:
	docker run -it --rm -v${PWD}:/app -w/app yohang/finite php ./vendor/bin/phpunit .
