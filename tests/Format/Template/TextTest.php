<?php
namespace nochso\Diff\Format\Template;

use nochso\Diff\ContextDiff;
use nochso\Diff\Diff;
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
        $diff = Diff::create($from, $to);
        $formatter = new Text();
        $output = $formatter->format($diff);
        $this->assertSame($expected, $output);
    }

    public function contextProvider()
    {
        return TestProvider::fromFile('text.context.txt');
    }

    /**
     * @dataProvider contextProvider
     *
     * @param int    $maxContext
     * @param string $from
     * @param string $to
     * @param string $expected
     */
    public function testContext($maxContext, $from, $to, $expected)
    {
        $context = new ContextDiff();
        $context->setMaxContext($maxContext);
        $diff = Diff::create($from, $to, $context);
        $formatter = new Text();
        $this->assertSame($expected, $formatter->format($diff));
    }
}
