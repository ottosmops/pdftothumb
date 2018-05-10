<?php

namespace Ottosmops\Pdftothumb\Test;

use PHPUnit\Framework\TestCase;

use Ottosmops\Pdftothumb\Converter;

use Ottosmops\Pdftothumb\Exceptions\CouldNotConvertPdf;
use Ottosmops\Pdftothumb\Exceptions\FileNotFound;
use Ottosmops\Pdftothumb\Exceptions\BinaryNotFound;
use Ottosmops\Pdftothumb\Exceptions\FileFormatNotAllowed;

class PdftothumbTest extends TestCase
{
    protected $src_path = __DIR__.'/testfiles/';

    protected $tmp_dir = __DIR__.'/testfiles/tmp/';

    protected $correct_file = 'correct.pdf';


    protected function setUp()
    {
        is_dir($this->tmp_dir) || mkdir($this->tmp_dir, 0777);
    }

    protected function tearDown()
    {
        putenv('PATH=$PATH:/usr/local/bin/:/usr/bin/:/bin/');
        exec("rm -r ".__DIR__.'/testfiles/tmp');
    }

    /** @test */
    public function it_creates_a_file()
    {
        putenv('PATH=$PATH:/usr/local/bin/:/usr/bin');
        $thumb = (new Converter($this->src_path . $this->correct_file))->convert();
        $actual = is_file($this->src_path .'correct.jpg');
        $this->assertTrue($actual);
        $this->assertSame('image/jpeg', mime_content_type($this->src_path .'correct.jpg'));
        unlink($this->src_path .'correct.jpg');
    }

    /** @test */
    public function it_creates_a_tiff()
    {
        putenv('PATH=$PATH:/usr/local/bin/:/usr/bin');
        $thumb = Converter::create($this->src_path . $this->correct_file)
                            ->format('tiff')
                            ->convert();
        $actual = is_file($this->src_path .'correct.tif');
        $this->assertTrue($actual);
        $this->assertSame('image/tiff', mime_content_type($this->src_path .'correct.tif'));
        unlink($this->src_path .'correct.tif');
    }

     /** @test */
    public function it_creates_a_png()
    {
        putenv('PATH=$PATH:/usr/local/bin/:/usr/bin');
        Converter::create($this->src_path . $this->correct_file)
                            ->format('png')
                            ->convert();
        $actual = is_file($this->src_path .'correct.png');
        $this->assertTrue($actual);
        $this->assertSame('image/png', mime_content_type($this->src_path .'correct.png'));
        unlink($this->src_path .'correct.png');
    }

    /** @test */
    public function the_image_has_the_right_dimention()
    {
        putenv('PATH=$PATH:/usr/local/bin/:/usr/bin');
        Converter::create($this->src_path . $this->correct_file)
                            ->format('jpeg')
                            ->scaleTo(200)
                            ->convert();
        list($width, $height) = getimagesize($this->src_path .'correct.jpg');
        $expected = $width == 200 ? 200 : $height;
        $this->assertSame($expected, 200);
        unlink($this->src_path .'correct.jpg');
    }

    /** @test */
    public function we_can_set_all_options_by_hand()
    {
        putenv('PATH=$PATH:/usr/local/bin/:/usr/bin');
        Converter::create($this->src_path . $this->correct_file)
                 ->target($this->src_path .'correct.jpg')
                 ->format('jpeg')
                 ->scaleTo(200)
                 ->page(1)
                 ->convert();
        $actual = is_file($this->src_path .'correct.jpg');
        $this->assertTrue($actual);
        $this->assertSame('image/jpeg', mime_content_type($this->src_path .'correct.jpg'));
        unlink($this->src_path .'correct.jpg');
    }

    /** @test */
    public function we_can_specify_a_target()
    {
        putenv('PATH=$PATH:/usr/local/bin/:/usr/bin');
        $target = $this->tmp_dir .'correct.jpg';
        Converter::create($this->src_path . $this->correct_file)
                            ->target($target)
                            ->convert();
        $actual = is_file($target);
        unlink($target);
        $this->assertTrue($actual);
    }

    /** @test */
    public function we_can_specify_a_format()
    {
        putenv('PATH=$PATH:/usr/local/bin/:/usr/bin');
        $target = $this->tmp_dir . 'correct.tif';
        Converter::create($this->src_path . $this->correct_file, $target)
                            ->format('tif')
                            ->convert();
        $actual = is_file($target);
        unlink($target);
        $this->assertTrue($actual);
    }

    /** @test */
    public function we_can_specify_a_executable()
    {
        $converter = Converter::create($this->src_path . $this->correct_file)
                            ->executable('docx');
        $expected = 'docx';
        $actual = $converter->executable;
        $this->assertSame($expected, $actual);
        $actual = $converter->command();
        $this->assertStringStartsWith($expected, $actual);
    }

    /** @test */
    public function we_can_set_addional_options()
    {
        $converter = Converter::create($this->src_path . $this->correct_file)
                            ->addOption('-gray');
        $actual = $converter->command();
        $expected = 'pdftoppm -jpeg -f 1 -l 1 -scale-to 150 -gray';
        $this->assertStringStartsWith($expected, $actual);
    }

    /** @test */
    public function we_can_set_commpletly_new_options()
    {
        $converter = Converter::create($this->src_path . $this->correct_file)
                            ->setOptions('-tiff -f 2 -l 2 -scale-to 200', false);
        $actual = $converter->command();
        $expected = 'pdftoppm -tiff -f 2 -l 2 -scale-to 200';
        $this->assertStringStartsWith($expected, $actual);
    }

    /** @test */
    public function it_will_throw_an_exception_when_the_file_format_is_not_allowed()
    {
        $this->expectException(FileFormatNotAllowed::class);
        Converter::create($this->src_path . $this->correct_file)
                            ->format('docx')
                            ->convert();
    }

    /** @test */
    public function it_will_throw_an_exception_when_the_file_is_not_found()
    {
        $this->expectException(FileNotFound::class);
        Converter::create($this->src_path . 'xyz')
                            ->convert();
    }

    /** @test */
    public function it_will_throw_an_exception_when_file_is_not_converted()
    {
        $this->expectException(CouldNotConvertPdf::class);
        Converter::create($this->src_path . 'corrupted-pdf.pdf')
                            ->convert();
    }

}
