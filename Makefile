all: dist

dist:
	mkdir -p build/random-featured-image
	cp -r src/* build/random-featured-image/
	cd build
	zip -r random-featured-image.zip random-featured-image 
	touch dist

clean:
	rm dist
	rm random-featured-image.zip
