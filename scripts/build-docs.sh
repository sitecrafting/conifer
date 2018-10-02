#!/usr/bin/env bash

echo "Building documentation..."

yarn gitbook install
sudo yarn gitbook build

ls -la _book