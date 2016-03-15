<?php
/*
 * This file is part of the Diff package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace nochso\Diff;

use nochso\Omni\EOL;
use PHPUnit_Framework_TestCase;
use nochso\Diff\LCS\MemoryEfficientImplementation;
use nochso\Diff\LCS\TimeEfficientImplementation;

class DifferTest extends PHPUnit_Framework_TestCase
{
    const REMOVED = 2;
    const ADDED   = 1;
    const OLD     = 0;

    /**
     * @var Differ
     */
    private $differ;

    protected function setUp()
    {
        $this->differ = new Differ;
    }

    /**
     * @param array  $expected
     * @param string $from
     * @param string $to
     * @dataProvider arrayProvider
     * @covers       nochso\Diff\Differ::diffToArray
     * @covers       nochso\Diff\LCS\TimeEfficientImplementation
     */
    public function testArrayRepresentationOfDiffCanBeRenderedUsingTimeEfficientLcsImplementation(array $expected, $from, $to)
    {
        $this->assertEquals($expected, $this->differ->diffToArray($from, $to, new TimeEfficientImplementation));
    }

    /**
     * @param string $expected
     * @param string $from
     * @param string $to
     * @dataProvider textProvider
     * @covers       nochso\Diff\Differ::diff
     * @covers       nochso\Diff\LCS\TimeEfficientImplementation
     */
    public function testTextRepresentationOfDiffCanBeRenderedUsingTimeEfficientLcsImplementation($expected, $from, $to)
    {
        $this->assertEquals($expected, $this->differ->diff($from, $to, new TimeEfficientImplementation));
    }

    /**
     * @param array  $expected
     * @param string $from
     * @param string $to
     * @dataProvider arrayProvider
     * @covers       nochso\Diff\Differ::diffToArray
     * @covers       nochso\Diff\LCS\MemoryEfficientImplementation
     */
    public function testArrayRepresentationOfDiffCanBeRenderedUsingMemoryEfficientLcsImplementation(array $expected, $from, $to)
    {
        $this->assertEquals($expected, $this->differ->diffToArray($from, $to, new MemoryEfficientImplementation));
    }

    /**
     * @param string $expected
     * @param string $from
     * @param string $to
     * @dataProvider textProvider
     * @covers       nochso\Diff\Differ::diff
     * @covers       nochso\Diff\LCS\MemoryEfficientImplementation
     */
    public function testTextRepresentationOfDiffCanBeRenderedUsingMemoryEfficientLcsImplementation($expected, $from, $to)
    {
        $this->assertEquals($expected, $this->differ->diff($from, $to, new MemoryEfficientImplementation));
    }

    /**
     * @covers nochso\Diff\Differ::diff
     */
    public function testCustomHeaderCanBeUsed()
    {
        $differ = new Differ(['CUSTOM HEADER']);

        $this->assertEquals(
            "CUSTOM HEADER\n@@ @@\n-a\n+b\n",
            $differ->diff('a', 'b')
        );
    }

    public function testTypesOtherThanArrayAndStringCanBePassed()
    {
        $this->assertEquals(
            "--- Original\n+++ New\n@@ @@\n-1\n+2\n",
            $this->differ->diff(1, 2)
        );
    }

    /**
     * @dataProvider lineEndingProvider
     */
    public function testLineEndingWarning($from, $to, $expectedFromEol, $expectedToEol)
    {
        $diff = $this->differ->diff($from, $to);
        // No warning
        if ($expectedFromEol === null) {
            $this->assertNotRegExp('/^ #Warning: Line ending changed from .+ to .+$/m', $diff);

            return;
        }

        $fromEolName = (new EOL($expectedFromEol))->getName();
        $toEolName   = (new EOL($expectedToEol))->getName();
        $pattern     = sprintf('/^ #Warning: Line ending changed from %s to %s$/m', preg_quote($fromEolName), preg_quote($toEolName));
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

    public function arrayProvider()
    {
        return array(
            array(
                array(
                    array('a', self::REMOVED),
                    array('b', self::ADDED)
                ),
                'a',
                'b'
            ),
            array(
                array(
                    array('ba', self::REMOVED),
                    array('bc', self::ADDED)
                ),
                'ba',
                'bc'
            ),
            array(
                array(
                    array('ab', self::REMOVED),
                    array('cb', self::ADDED)
                ),
                'ab',
                'cb'
            ),
            array(
                array(
                    array('abc', self::REMOVED),
                    array('adc', self::ADDED)
                ),
                'abc',
                'adc'
            ),
            array(
                array(
                    array('ab', self::REMOVED),
                    array('abc', self::ADDED)
                ),
                'ab',
                'abc'
            ),
            array(
                array(
                    array('bc', self::REMOVED),
                    array('abc', self::ADDED)
                ),
                'bc',
                'abc'
            ),
            array(
                array(
                    array('abc', self::REMOVED),
                    array('abbc', self::ADDED)
                ),
                'abc',
                'abbc'
            ),
            array(
                array(
                    array('abcdde', self::REMOVED),
                    array('abcde', self::ADDED)
                ),
                'abcdde',
                'abcde'
            )
        );
    }

    public function textProvider()
    {
        return array(
            array(
                "--- Original\n+++ New\n@@ @@\n-a\n+b\n",
                'a',
                'b'
            ),
            array(
                "--- Original\n+++ New\n@@ @@\n-ba\n+bc\n",
                'ba',
                'bc'
            ),
            array(
                "--- Original\n+++ New\n@@ @@\n-ab\n+cb\n",
                'ab',
                'cb'
            ),
            array(
                "--- Original\n+++ New\n@@ @@\n-abc\n+adc\n",
                'abc',
                'adc'
            ),
            array(
                "--- Original\n+++ New\n@@ @@\n-ab\n+abc\n",
                'ab',
                'abc'
            ),
            array(
                "--- Original\n+++ New\n@@ @@\n-bc\n+abc\n",
                'bc',
                'abc'
            ),
            array(
                "--- Original\n+++ New\n@@ @@\n-abc\n+abbc\n",
                'abc',
                'abbc'
            ),
            array(
                "--- Original\n+++ New\n@@ @@\n-abcdde\n+abcde\n",
                'abcdde',
                'abcde'
            ),
        );
    }
}
