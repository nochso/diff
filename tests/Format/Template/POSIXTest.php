<?php
namespace nochso\Diff\Format\Template;

use nochso\Diff\Differ;
use nochso\Diff\Format\CliEscaper;
use nochso\Diff\TestProvider;

class POSIXTest extends \PHPUnit_Framework_TestCase
{
    public function formatProvider()
    {
        return TestProvider::fromFile('posix.txt');
    }

    /**
     * @dataProvider formatProvider
     *
     * @param string $from
     * @param string $to
     * @param string $expected
     */
    public function testFormat($from, $to, $expected)
    {
        $diff = new Differ();
        $output = $diff->diff($from, $to, null, new POSIX());
        $this->assertSame($expected, $output);
    }

    public function cliEscaperProvider()
    {
        return TestProvider::fromFile('posix.cliescape.txt');
    }

    /**
     * @dataProvider cliEscaperProvider
     *
     * @param string $from
     * @param string $to
     * @param string $expected
     */
    public function testCliEscaper($from, $to, $expected)
    {
        $diff = new Differ();
        $formatter = new POSIX();
        $formatter->setEscaper(new CliEscaper());
        $output = $diff->diff($from, $to, null, $formatter);
        $this->assertSame($expected, $output);
    }

    public function testCliEscaper_MixedEol()
    {
        $from = "a\nb\nc\nd\r\ne";
        $to = "a\nb\nc\n\r\ne";
        $expected = <<<TAG
1: "a"
2: "b"
3: "c"
4: \e[31m"d\\r"\e[0m
 : \e[32m"\\r"\e[0m
5: "e"
TAG;
        $this->testCliEscaper($from, $to, $expected);
    }
}
