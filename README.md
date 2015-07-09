# MistralPay for OpenCart

[![Latest Version](https://img.shields.io/github/release/mistralpayltd/opencart-plugin.svg?style=flat-square)](https://github.com/bitpay/opencart-plugin/releases)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status](https://img.shields.io/travis/MistralpayLTD/opencart-plugin/master.svg?style=flat-square)](https://travis-ci.org/bitpay/opencart-plugin)

## Installation

PLease, follow the instructions found in the [MistralPay for OpenCart Guide](GUIDE.md)

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
