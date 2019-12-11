DIR=$(dir $(realpath $(firstword $(MAKEFILE_LIST))))

test: test-7.1 test-7.2 test-7.3 test-7.4

test-7.1:
	@docker run --rm \
		-v ${DIR}:${DIR} \
		-w ${DIR} \
		php:7.1-cli \
		./vendor/bin/phpunit

test-7.2:
	@docker run --rm \
		-v ${DIR}:${DIR} \
		-w ${DIR} \
		php:7.2-cli \
		./vendor/bin/phpunit

test-7.3:
	@docker run --rm \
		-v ${DIR}:${DIR} \
		-w ${DIR} \
		php:7.3-cli \
		./vendor/bin/phpunit

test-7.4:
	@docker run --rm \
		-v ${DIR}:${DIR} \
		-w ${DIR} \
		php:7.4-cli \
		./vendor/bin/phpunit
