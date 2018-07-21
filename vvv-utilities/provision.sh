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


# install cypress binary if not already installed
if ! [[ -d "~/.cache/Cypress" ]] ; then
  echo 'Installing Cypress...'

  sudo mkdir -p ~/.cache/Cypress
  sudo chmod 777 -R ~/.cache/
  sudo chmod 777 -R ~/.npm/
  sudo chmod 777 -R /usr/lib/node_modules

  CYPRESS_INSTALL_BINARY=0 sudo npm install -g cypress
fi
