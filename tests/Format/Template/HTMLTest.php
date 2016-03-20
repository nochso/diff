<?php
namespace nochso\Diff\Format\Template;

use nochso\Diff\Diff;
use nochso\Diff\TestProvider;

class HTMLTest extends \PHPUnit_Framework_TestCase
{
    public function formatProvider()
    {
        return TestProvider::fromFile('html.format.txt');
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
        $diff = Diff::create($from, $to);
        $output = (new HTML())->format($diff);
        $this->assertSame($expected, $output);
    }

    public function showLineNumberProvider()
    {
        return TestProvider::fromFile('html.linenumber.txt');
    }

    /**
     * @dataProvider showLineNumberProvider
     *
     * @param string $from
     * @param string $to
     * @param string $expected
     */
    public function testShowLineNumber($from, $to, $expected)
    {
        $formatter = new HTML();
        $formatter->showLineNumber();
        $output = $formatter->format(Diff::create($from, $to));
        $this->assertSame($expected, $output);
    }
}
