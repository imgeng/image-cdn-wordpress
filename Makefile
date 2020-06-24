.PHONY: % dist-clean dist make-zip svn

dist-clean:
	rm -rf dist/image_cdn

dist: dist-clean make-zip

make-zip:
	mkdir -p dist/image_cdn
	cp -v -r *.php *.txt imageengine assets templates dist/image_cdn/
	cd dist && zip -9 -r image-cdn-$$(grep -o 'Version: .*' ../image-cdn.php | cut -d ' ' -f2).zip image_cdn
	rm -rf dist/image_cdn

svn:
	cp -v -r plugin-assets/* svn/assets/
	cp -v -r *.php *.txt imageengine assets templates svn/trunk

phpcs:
	../phpcs/bin/phpcs --standard=WordPress .

phpcs-fix:
	../phpcs/bin/phpcbf --standard=WordPress .
