<?php
namespace nochso\Diff\Format\Template;

use nochso\Diff\Diff;
use nochso\Diff\Escape;
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
        $output = (new POSIX())->format(Diff::create($from, $to));
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
        $formatter = new POSIX();
        $formatter->setEscaper(new Escape\Cli());
        $output = $formatter->format(Diff::create($from, $to));
        $this->assertSame($expected, $output);
    }

    public function testCliEscaper_MixedEol()
    {
        $from = "a\nb\nc\nd\r\ne";
        $to = "a\nb\nc\n\r\ne";
        $expected = <<<TAG
\e[33m1\e[0m "a"
\e[33m2\e[0m "b"
\e[33m3\e[0m "c"
\e[33m4\e[0m \e[31m"d"\e[0m
\e[33m \e[0m \e[32m""\e[0m
\e[33m5\e[0m "e"
TAG;
        $this->testCliEscaper($from, $to, $expected);
    }
}
