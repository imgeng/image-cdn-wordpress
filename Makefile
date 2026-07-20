.PHONY: % dist-clean dist make-zip svn test check fix

VERSION := $(shell sed -n 's/^[[:space:]]*\*[[:space:]]*Version:[[:space:]]*\([0-9][0-9.]*\).*/\1/p' image-cdn.php)
FILE := image-cdn-$(VERSION).zip

dist-clean:
	rm -rf dist/image_cdn

dist: dist-clean make-zip

make-zip:
	rm dist/${FILE} || echo -n ""
	mkdir -p dist/image_cdn
	cp -v -r *.php *.txt imageengine assets templates dist/image_cdn/
	cd dist && zip -9 -r ${FILE} image_cdn
	rm -rf dist/image_cdn

test:
	vendor/bin/phpunit -vvv -c phpunit-standalone.xml.dist

fix:
	vendor/bin/phpcbf -v

check:
	vendor/bin/phpcs

# svn:
# 	cp -v -r plugin-assets/* svn/assets/
# 	cp -v -r *.php *.txt imageengine assets templates svn/trunk
