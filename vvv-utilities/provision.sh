#!/usr/bin/env bash

# install Cypress dependency for headless browsing
echo 'Installing xvfb...'
sudo apt-get install -y xvfb


echo 'Installing global npm tooling...'

if [[ -z $(which yarn) ]] ; then
  npm install -g yarn
fi

if [[ -z $(which newman) ]] ; then
  npm install -g newman
fi

