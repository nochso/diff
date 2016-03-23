<?php
namespace nochso\Diff\Format\Template;

use nochso\Diff\Diff;
use nochso\Diff\TestProvider;

class HTMLTest extends \PHPUnit_Framework_TestCase
{
    public function formatProvider()
    {
        return TestProvider::fromFile('html.txt');
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
        $formatter = new HTML();
        $output = $formatter->format(Diff::create($from, $to));
        $this->assertSame($expected, $output);
    }

    public function formatWithoutLineNumberProvider()
    {
        return TestProvider::fromFile('html.without.linenumber.txt');
    }

    /**
     * @dataProvider formatWithoutLineNumberProvider
     *
     * @param string $from
     * @param string $to
     * @param string $expected
     */
    public function testFormatWithoutLineNumber($from, $to, $expected)
    {
        $diff = Diff::create($from, $to);
        $formatter = new HTML();
        $formatter->getPrintf()->disableLineNumber();
        $output = $formatter->format($diff);
        $this->assertSame($expected, $output);
    }
}
