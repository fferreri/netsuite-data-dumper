# Netsuite Data Dumper

The Netsuite Data Dumper (NSDD) is a [Symfony Console Application](http://symfony.com/doc/current/components/console/index.html) that helps in downloading raw data from Netsuite. It extracts most of the supported record types and stores them as JSON files. All is done using Netsuite's PHP Toolkit through the great Ryan Winchester's ["NetSuite PHP API Client"](https://github.com/fungku/netsuite-php). 

## Requirements

NSDD requires PHP 5.5+

## Installation

The supported way of installing NSDD is via Composer.

```
$ composer create-project "fferreri/netsuite-data-dumper:dev-master" <directory-name>
```

## Configuration

Before running `dumper.php` you must supply your Netsuite credentials through the 'config/general.ini' configuration file. 
 
```
[netsuite]
endpoint = 2015_1
host     = https://webservices.netsuite.com
email    = your@netuiteusername
password = your_password
role     = your role id
account  = your account number

[debug]
enabled  = false
```

You usually need to edit the email, password, role and account fields. That information is available in Netsuite's setup area.  

## Usage

NSDD is designed to be very simple and straightforward to use. All you can do with it is to download records from Netsuite and export those records into a CSV file to be inserted into your favorite database. 

Run `$ dumper.php` command from your terminal to see the available commands and the supported parameters. 

```
$ ./dumper.php
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
  ns:get     Download records from NetSuite
```

### ns:get
```
Usage:
  ns:get [options] [--] [<entity>]

Arguments:
  entity                       The entity type name to count

Options:
      --pageSize[=PAGESIZE]    The page size [default: 50]
      --startPage[=STARTPAGE]  The start page index (index base is 1) [default: 1]
      --endPage[=ENDPAGE]      The end page index (index base is 1) [default: 9999999999]
      --count                  Counts records and prints the result.
```

### ns:dump
```
Usage:
  ns:dump [options]

Options:
      --pageSize[=PAGESIZE]  The page size [default: 50]
      --count                Counts records and prints the result.
```

### ns:export
```
Usage:
  ns:export [options]

Options:
  -e, --entity=ENTITY      The entity type name to export
  -f, --fields=FIELDS      The fields to export
      --outfile[=OUTFILE]  The output file path and name
      --skip[=SKIP]        The number of records to skip [default: 0]
      --max[=MAX]          The number of records to export [default: 9999999999]
```

## License

Original work is Copyright NetSuite Inc. 2015 and provided "as is."
