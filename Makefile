.PHONY: % dist-clean dist make-zip svn test check fix

VERSION := $(shell sed -n 's/^[[:space:]]*\*[[:space:]]*Version:[[:space:]]*\([0-9][0-9.]*\).*/\1/p' image-cdn.php)
SLUG := image-cdn
FILE := $(SLUG)-$(VERSION).zip

dist-clean:
	rm -rf dist/$(SLUG)

dist: dist-clean make-zip

make-zip:
	@test -f config/APIData.php || { echo "ERROR: config/APIData.php is missing; the built plugin will not work. Create it from vendor/imageengine/php-sdk/config/APIData.sample.php"; exit 1; }
	rm dist/${FILE} || echo -n ""
	mkdir -p dist/$(SLUG)
	cp -v -r *.php *.txt composer.json imageengine assets templates config vendor dist/$(SLUG)/
	cd dist && zip -9 -r ${FILE} $(SLUG)
	rm -rf dist/$(SLUG)

test:
	vendor/bin/phpunit -vvv -c phpunit-standalone.xml.dist

fix:
	vendor/bin/phpcbf -v

check:
	vendor/bin/phpcs

# svn:
# 	cp -v -r plugin-assets/* svn/assets/
# 	cp -v -r *.php *.txt imageengine assets templates svn/trunk
