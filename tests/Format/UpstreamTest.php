<?php
namespace nochso\Diff\Format;

use nochso\Diff\Diff;
use nochso\Omni\EOL;

class UpstreamTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers nochso\Diff\Format\Upstream::format
     * @covers nochso\Diff\Format\Upstream::setHeader
     */
    public function testCustomHeaderCanBeUsed()
    {
        $formatter = new Upstream();
        $formatter->setHeader(['CUSTOM HEADER']);
        $diff = Diff::create('a', 'b');
        $this->assertEquals("CUSTOM HEADER\n@@ @@\n-a\n+b\n", $formatter->format($diff));
    }

    /**
     * @dataProvider lineEndingProvider
     */
    public function testLineEndingWarning($from, $to, $expectedFromEol, $expectedToEol)
    {
        $formatter = new Upstream();
        $diff = $formatter->format(Diff::create($from, $to));
        // No warning
        if ($expectedFromEol === null) {
            $this->assertNotRegExp('/#Warning: Line ending changed from .+ to .+$/m', $diff);
            return;
        }
        $fromEolName = (new EOL($expectedFromEol))->getName();
        $toEolName = (new EOL($expectedToEol))->getName();
        $pattern = sprintf('/^#Warning: Line ending changed from %s to %s$/m', preg_quote($fromEolName), preg_quote($toEolName));
        $this->assertRegExp($pattern, $diff);
    }

    public function lineEndingProvider()
    {
        return [
            ["a\nb", "a\nb", null, null],
            ["a\nb", "a\r\nb", EOL::EOL_LF, EOL::EOL_CR_LF],
            ["a\rb", "a\n\nb", EOL::EOL_CR, EOL::EOL_LF],
        ];
    }
}
