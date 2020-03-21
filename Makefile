.PHONY: % dist

dist-clean:
	rm -rf dist/image_cdn

dist: dist-clean make-zip

make-zip:
	mkdir -p dist/image_cdn
	cp -v -r *.php *.txt ImageEngine assets dist/image_cdn/
	cd dist && zip -9 -r image-cdn-0.0.0.zip image_cdn
	rm -rf dist/image_cdn
