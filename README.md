# MistralPay for OpenCart - Sample Code

!Alert! This is an alpha development pre-release, it use experimental features yet to be supported by the production server  there could be strong changes, use at your own risk.

[![Latest Version](https://img.shields.io/github/release/MistralpayLTD/opencart-plugin.svg?style=flat-square)](https://github.com/MistralPayLTD/opencart-plugin/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://api.travis-ci.org/MistralPayLTD/opencart-plugin.svg)](https://travis-ci.org/MistralPayLTD/opencart-plugin)

## Installation

Please, follow the instructions found in the [MistralPay for OpenCart Guide](GUIDE.md)

## Development Setup

``` bash
# Clone the repo
$ git clone https://github.com/mistralpayltd/opencart-plugin.git
$ cd ./opencart-plugin

# Install dependencies via Composer
$ composer install

# Set Environment Variables (variables needed can be found in .env.sample)
$ cp .env.sample .env

# After modifying the Environment Variables for your environment setup OpenCart
$ ./bin/robo setup
```

## Development Workflow

``` bash
# Run PHP Server of OpenCart installation and redirect bash I/O
$ ./bin/robo server &

# Watch for source code changes and copy them to the OpenCart installation
$ ./bin/robo watch
```

## Testing

``` bash
$ ./bin/robo test
```

## Build

``` bash
$ ./bin/robo build

# Outputs:
# ./build/mistralpay-opencart - the distribution files
# ./build/mistralpay-opencart.ocmod.zip - the distribution archive
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
