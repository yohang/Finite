.PHONY: build cli test test_all_targets psalm

build:
	dagger call build

cli:
	dagger call cli

test:
	dagger call test

test_all_targets:
	dagger call test-all

psalm:
	dagger call psalm
