[ ![Codeship Status for ngabor84/ecc](https://app.codeship.com/projects/d0ae3480-5798-0136-cad2-6231e092a7cb/status?branch=master)](https://app.codeship.com/projects/294979)
[![GitHub license](https://img.shields.io/github/license/ngabor84/ecc.svg)](https://github.com/ngabor84/ecc/blob/master/LICENSE)

# Environment Consistency Checker

CLI tool for check environment variables consistency between several environments

## Installation
### with [phive](https://github.com/phar-io/phive)
```shell
phive install ecc
```
### manually
```shell
# download the phar file and the public key
wget https://github.com/ngabor84/ecc/releases/download/0.1.0/ecc.phar
wget https://github.com/ngabor84/ecc/releases/download/0.1.0/ecc.phar.asc

# verify the phar with gpg
gpg --keyserver pgp.mit.edu --recv-keys 0xcd54be34da0a1a97
gpg --verify ecc.phar.asc ecc.phar

# add execution permission
sudo chmod +x ecc.phar

# move the downloaded files into usr/local/bin
sudo mv ecc.phar /usr/local/bin/ecc
```

## Usage
```shell
# Check all env files in a directory (recursively)
ecc check /path/to/env/files/directory/
# Check separate env files
ecc check /path/to/env/files/.env.test /path/to/env/files/.env.dev /path/to/env/files/.env.stage /path/to/env/files/.env.prod
# Check env files in a directory and separete env files also
ecc check /path/to/env/files/directory/ /path/to/env/files/.env.test /path/to/env/files/.env.dev
```