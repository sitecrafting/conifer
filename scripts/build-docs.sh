#!/usr/bin/env bash

echo "Building documentation..."

./vendor/victorjonsson/markdowndocs/bin/phpdoc-md

yarn gitbook install
yarn gitbook build
