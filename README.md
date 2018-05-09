# Extract text from a PDF with pdftothumb

[![Software License](https://img.shields.io/badge/license-MIT-blue.svg?style=flat-square)](LICENSE.md)
[![Latest Stable Version](https://poser.pugx.org/ottosmops/pdftothumb/v/stable?format=flat-square)](https://packagist.org/packages/ottosmops/pdftothumb)
[![Build Status](https://img.shields.io/travis/ottosmops/pdftothumb/master.svg?style=flat-square)](https://travis-ci.org/ottomops/pdftothumb)
[![Packagist Downloads](https://img.shields.io/packagist/dt/ottosmops/pdftothumb.svg?style=flat-square)](https://packagist.org/packages/ottosmops/pdftothumb)

This package provides a wrapper for `pdftoppm`. 

```php
  \Ottosmops\Pdftothumb\Converter::create('/path/to/file.pdf')->convert(); //creates a thumb of the first page: '/path/to/file.jpg' 
```

## Requirements

The Package uses [pdftoppm](https://linux.die.net/man/1/pdftoppm). Make sure that this is installed: ```which pdftoppm```

For Installation see:
[poppler-utils](https://linuxappfinder.com/package/poppler-utils)

If the installed binary is not found ("```The command "which pdftoppm" failed.```") you can pass the full path to the ```_constructor``` (see below) or use ```putenv('PATH=$PATH:/usr/local/bin/:/usr/bin')``` (with the dir where pdftoppm lives) before you call the class ```Converter```.

## Installation

```bash
composer require ottosmops/pdftothumb
```

## Usage

Converting PDF to jpg:
```php
$exitCode = (new Converter($source))->convert();
```

Or like this: 
```php
$converter = Converter::create($source);
$converter->convert()
```

You can set some options:
```php
Converter::create('/path/to/source.pdf')
                 ->target('/path/to/target.jpg')
                 ->format('jpeg') // jpeg | png | tiff
                 ->scaleTo(150)
                 ->page(1) // or ->firstpage(1)->lastpage(1)
                 ->convert();
```

You can add options:
```php
Converter::create('/path/to/source.pdf')
                ->addOption('-gray') 
                ->convert();
```
 
Or you can replace all options and set them by hand:
```php 
Converter::create('/path/to/source.pdf')
                ->setOptions('-f 3 -l 3 -scale-to 200 -png')
                ->convert();
```

Default options are: ```-f 1 -l 1 -scale-to 150 -jpeg```

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

