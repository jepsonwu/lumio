build:
	composer dump-autoload

install:
	composer update

test:
	vendor/bin/phpunit

sniffer:
	vendor/bin/phpcs   --report=full --colors  --standard=style.xml --extensions=php modules app

fixer:
	vendor/bin/phpcbf  --standard=style.xml --extensions=php  modules app

doc:
	apidoc   -i modules -o api/doc