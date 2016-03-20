<?php
namespace nochso\Diff\Format\Template;

use nochso\Diff\Differ;
use nochso\Diff\TestProvider;

class TextTest extends \PHPUnit_Framework_TestCase
{
    public function textProvider()
    {
        return TestProvider::fromFile('text.txt');
    }

    /**
     * @dataProvider textProvider
     *
     * @param string $from
     * @param string $to
     * @param string $expected
     */
    public function testText($from, $to, $expected)
    {
        $differ = new Differ();
        $output = $differ->diff($from, $to, null, new Text());
        $this->assertSame($expected, $output);
    }

    public function contextProvider()
    {
        return TestProvider::fromFile('text.context.txt');
    }

    /**
     * @dataProvider contextProvider
     *
     * @param int    $context
     * @param string $from
     * @param string $to
     * @param string $expected
     */
    public function testContext($context, $from, $to, $expected)
    {
        $differ = new Differ();
        $formatter = new Text();
        $formatter->getContextDiff()->setMaxContext($context);

        $output = $differ->diff($from, $to, null, $formatter);
        $this->assertSame($expected, $output);
    }
}
