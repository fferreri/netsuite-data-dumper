# Netsuite Data Dumper

The Netsuite Data Dumper (NSDD) Helps in downloading data from Netsuite. It extracts most of the supported record types and stores them as JSON files. All is done using Netsuite's PHP Toolkit. 

## Requirements

NSDD requires PHP 5.5+

## Installation

The supported way of installing NSDD is via Composer.

`$ composer require fferreri/netsuite-data-dumper`

## Usage

NSDD is designed to be very simple and straightforward to use. All you can do with it is to download records from Netsuite and export those records into a CSV file to be inserted into your favorite database. 

Run `$ dumper.php` command from your terminal to see the available commands and the supported parameters. 

`$ ./dumper.php
NetsuiteDumper version 1.0

Usage:
  command [options] [arguments]

Options:
  -h, --help            Display this help message
  -q, --quiet           Do not output any message
  -V, --version         Display this application version
      --ansi            Force ANSI output
      --no-ansi         Disable ANSI output
  -n, --no-interaction  Do not ask any interactive question
  -v|vv|vvv, --verbose  Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug

Available commands:
  help       Displays help for a command
  list       Lists commands
 ns
  ns:dump    Download all records from all NetSuite known record types
  ns:export  Export records to CSV format
  ns:get     Download records from NetSuite`




