# Convert a PDF to an image with pdftoppm 

[![GitHub license](https://img.shields.io/github/license/ottosmops/pdftothumb.svg)](https://github.com/ottosmops/pdftothumb/blob/master/LICENSE.md)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ottosmops/pdftothumb/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ottosmops/pdftothumb/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/ottosmops/pdftothumb/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/ottosmops/pdftothumb/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/ottosmops/pdftothumb/badges/build.png?b=master)](https://scrutinizer-ci.com/g/ottosmops/pdftothumb/build-status/master)
[![Latest Stable Version](https://poser.pugx.org/ottosmops/pdftothumb/v/stable?format=flat-square)](https://packagist.org/packages/ottosmops/pdftothumb)
[![Packagist Downloads](https://img.shields.io/packagist/dt/ottosmops/pdftothumb.svg?style=flat-square)](https://packagist.org/packages/ottosmops/pdftothumb)

This package provides a wrapper for `pdftoppm`. 

```php
  \Ottosmops\Pdftothumb\Converter::create('/path/to/file.pdf')->convert(); 
  //creates a thumb of the first page: '/path/to/file.jpg' 
```

We use this as an alternative to the excellent [spatie/pdf-to-image](https://github.com/spatie/pdf-to-image) package as we sometimes have large PDFs to convert and then it seems to be faster and more memory friendly to use pdftoppm. 

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
$exitCode = (new Converter($source, $target, $executable))->convert();
```

```$target``` and ```$executable``` are optional.

Or like this: 
```php
$converter = Converter::create($source);
$converter->convert()
```

You can set some options:
```php
Converter::create('/path/to/source.pdf')
                 ->target('/path/to/target.jpg')
                 ->executable('path/to/pdftoppm')
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

## Usage for spatie/medialibrary

Tell the medialibrary not to use the standard ImageGenarator.

config/medialibrary.php
```php
/*
* These generators will be used to created conversion of media files.
*/
'image_generators' => [
	Spatie\MediaLibrary\ImageGenerators\FileTypes\Image::class ,
	//Spatie\MediaLibrary\ImageGenerators\FileTypes\Pdf::class ,
	Spatie\MediaLibrary\ImageGenerators\FileTypes\Svg::class ,
	Spatie\MediaLibrary\ImageGenerators\FileTypes\Video::class ,
],
```

Create a new ImageGenerator 

app/ImageGenarators/Pdf.php

```php
<?php

namespace App\ImageGenerators;

use Illuminate\Support\Collection;
use Spatie\MediaLibrary\Conversion\Conversion;
use Spatie\MediaLibrary\ImageGenerators\BaseGenerator;
use Ottosmops\Pdftothumb\Converter;

class Pdf extends BaseGenerator
{
   /**
    * This function should return a path to an image representation of the given file.
    */
    public function convert(string $path, Conversion $conversion = null) : string
    {
        $imageFile = pathinfo($path, PATHINFO_DIRNAME).'/'.pathinfo($path, PATHINFO_FILENAME).'.jpg';

        Converter::create($path)->target($imageFile)->convert();

        return $imageFile;
    }

    public function requirementsAreInstalled() : bool
    {
        return true;
    }

    public function supportedExtensions() : Collection
    {
        return collect(['pdf']);
    }

    public function supportedMimeTypes() : Collection
    {
        return collect('application/pdf');
    }
}
```  

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

