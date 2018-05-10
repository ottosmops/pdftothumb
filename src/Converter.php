<?php

namespace Ottosmops\Pdftothumb;

use Symfony\Component\Process\Process;

use Ottosmops\Pdftothumb\Exceptions\CouldNotConvertPdf;
use Ottosmops\Pdftothumb\Exceptions\FileNotFound;
use Ottosmops\Pdftothumb\Exceptions\BinaryNotFound;
use Ottosmops\Pdftothumb\Exceptions\FileFormatNotAllowed;


class Converter
{
    public $executable = '';

    protected $options = [];

    protected $source = '';

    protected $target = '';

    protected $extension = '';

    // cf https://www.systutorials.com/docs/linux/man/1-pdftoppm/
    public $exitCodes = [
        0 => 'No error.',
        1 => 'Error opening a PDF file.',
        2 => 'Error opening an output file.',
        3 => 'Error related to PDF permissions.',
        99 => 'Other error.'
    ];


    public function __construct(string $source = '', string $target = '', $executable = '')
    {
        $this->executable = $executable ? $executable : 'pdftoppm';
        $this->format('jpeg');
        $this->extension('jpg');
        $this->page(1);
        $this->scaleTo(150);

        $this->source($source);
        if (!is_file($source)) {
            throw new FileNotFound("could not find pdf {$source}");
        }
        if (!$target) {
            $this->target = pathinfo($this->source, PATHINFO_DIRNAME).'/'.pathinfo($this->source, PATHINFO_FILENAME);
        } else {
            $this->target($target);
        }

    }

    public static function create(string $source = null, string $target = null, string $executable = null)
    {
        return (new static($source, $target, $executable));
    }

    public function executable(string $executable)
    {
        $this->executable = $executable;
        return $this;
    }

    protected function source(string $source)
    {
        $this->source = $source;
        return $this;
    }

    public function target(string $target = null)
    {
        $this->target = pathinfo($target, PATHINFO_DIRNAME).'/'.pathinfo($target, PATHINFO_FILENAME);
        return $this;
    }

    public function scaleTo(int $scaleTo)
    {
        $this->options['scale-to'] = '-scale-to '. (string) $scaleTo;
        return $this;
    }

    public function firstPage(int $firstPage)
    {
        $this->options['firstPage'] = '-f '. (string) $firstPage;
        return $this;
    }

    public function lastPage(int $lastPage)
    {
        $this->options['lastPage'] = '-l '. (string) $lastPage;
        return $this;
    }

    public function page(int $page)
    {
        $this->firstPage($page);
        $this->lastPage($page);
        return $this;
    }

    public function format(string $format)
    {
        $format = strtolower($format);
        $format = $format == 'jpg' ? 'jpeg' : $format;
        $format = $format == 'tif' ? 'tiff' : $format;

        $formats = ['jpeg', 'png', 'tiff'];

        if (!in_array($format, $formats)) {
            throw new FileFormatNotAllowed("fileformat not allowed {$format}");
        }

        $this->options['format'] = '-' . $format;

        if ($format == 'jpeg') {
            $this->extension('jpg');
        }
        if ($format == 'tiff') {
            $this->extension('tif');
        }
        if ($format == 'png') {
            $this->extension('png');
        }

        return $this;
    }

    protected function extension($extension = null)
    {
        $this->extension = $extension;
        return $this;
    }

    public function addOption(string $options)
    {
        $this->options[] = $options;
        return $this;
    }

    public function setOptions(string $options)
    {
        $this->options = [ $options ];
        return $this;
    }

    public function command()
    {
        $options = join(' ', $this->options);
        $command = "{$this->executable} {$options} '{$this->source}' > '{$this->target}.{$this->extension}'";
        return $command;
    }

    /**
     * extract text
     * @return string
     */
    public function convert()
    {
        $process = new Process($this->command());
        $process->run();

        if (!$process->isSuccessful()) {
            throw new CouldNotConvertPdf($process);
        }

        return $process->getExitCode();
    }
}
