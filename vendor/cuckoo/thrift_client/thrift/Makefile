build:
	@echo "\n--------------> build thrift <--------------\n"
	rm -rf packages/*
	thrift -nowarn --gen php:oop -out packages/ banyandb.thrift
