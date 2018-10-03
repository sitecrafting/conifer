#!/usr/bin/env bash

echo "Building documentation..."

DOCS_DIR=${1:-'./docs'}

yarn gitbook install $DOCS_DIR
yarn gitbook build $DOCS_DIR
