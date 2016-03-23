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

use nochso\Diff\Format\Upstream;
use nochso\Diff\LCS\MemoryEfficientImplementation;
use nochso\Diff\LCS\TimeEfficientImplementation;

class DifferTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Differ
     */
    private $differ;

    protected function setUp()
    {
        $this->differ = new Differ();
    }

    /**
     * @param array  $expected
     * @param string $from
     * @param string $to
     * @dataProvider arrayProvider
     * @covers       nochso\Diff\Differ::diffToArray
     * @covers       nochso\Diff\LCS\TimeEfficientImplementation
     */
    public function testArrayRepresentationOfDiffCanBeDiffedUsingTimeEfficientLcsImplementation(array $expected, $from, $to)
    {
        $this->assertEquals($expected, $this->differ->diffToArray($from, $to, new TimeEfficientImplementation()));
    }

    /**
     * @param string $expected
     * @param string $from
     * @param string $to
     * @dataProvider textProvider
     * @covers       nochso\Diff\Differ::diffToArray
     * @covers       nochso\Diff\LCS\TimeEfficientImplementation
     */
    public function testTextRepresentationOfDiffCanBeRenderedUsingTimeEfficientLcsImplementation($expected, $from, $to)
    {
        $diff = Diff::create($from, $to, null, new TimeEfficientImplementation());
        $formatter = new Upstream();
        $this->assertEquals($expected, $formatter->format($diff));
    }

    /**
     * @param array  $expected
     * @param string $from
     * @param string $to
     * @dataProvider arrayProvider
     * @covers       nochso\Diff\Format\Upstream::format
     * @covers       nochso\Diff\LCS\MemoryEfficientImplementation
     */
    public function testArrayRepresentationOfDiffCanBeDiffedUsingMemoryEfficientLcsImplementation(array $expected, $from, $to)
    {
        $this->assertEquals($expected, $this->differ->diffToArray($from, $to, new MemoryEfficientImplementation()));
    }

    /**
     * @param string $expected
     * @param string $from
     * @param string $to
     * @dataProvider textProvider
     * @covers       nochso\Diff\Format\Upstream::format
     * @covers       nochso\Diff\LCS\MemoryEfficientImplementation
     */
    public function testTextRepresentationOfDiffCanBeRenderedUsingMemoryEfficientLcsImplementation($expected, $from, $to)
    {
        $diff = Diff::create($from, $to, null, new MemoryEfficientImplementation());
        $formatter = new Upstream();
        $formatter->format($diff);
        $this->assertEquals($expected, $formatter->format($diff));
    }

    public function testTypesOtherThanArrayAndStringCanBePassed()
    {
        $expected = "--- Original\n+++ New\n@@ @@\n-1\n+2\n";
        $diff = Diff::create(1, 2);
        $formatter = new Upstream();
        $this->assertEquals($expected, $formatter->format($diff));
    }

    public function arrayProvider()
    {
        return [
            [
                [
                    ['a', Differ::REMOVE],
                    ['b', Differ::ADD],
                ],
                'a',
                'b',
            ],
            [
                [
                    ['ba', Differ::REMOVE],
                    ['bc', Differ::ADD],
                ],
                'ba',
                'bc',
            ],
            [
                [
                    ['ab', Differ::REMOVE],
                    ['cb', Differ::ADD],
                ],
                'ab',
                'cb',
            ],
            [
                [
                    ['abc', Differ::REMOVE],
                    ['adc', Differ::ADD],
                ],
                'abc',
                'adc',
            ],
            [
                [
                    ['ab', Differ::REMOVE],
                    ['abc', Differ::ADD],
                ],
                'ab',
                'abc',
            ],
            [
                [
                    ['bc', Differ::REMOVE],
                    ['abc', Differ::ADD],
                ],
                'bc',
                'abc',
            ],
            [
                [
                    ['abc', Differ::REMOVE],
                    ['abbc', Differ::ADD],
                ],
                'abc',
                'abbc',
            ],
            [
                [
                    ['abcdde', Differ::REMOVE],
                    ['abcde', Differ::ADD],
                ],
                'abcdde',
                'abcde',
            ],
        ];
    }

    public function textProvider()
    {
        return [
            [
                "--- Original\n+++ New\n@@ @@\n-a\n+b\n",
                'a',
                'b',
            ],
            [
                "--- Original\n+++ New\n@@ @@\n-ba\n+bc\n",
                'ba',
                'bc',
            ],
            [
                "--- Original\n+++ New\n@@ @@\n-ab\n+cb\n",
                'ab',
                'cb',
            ],
            [
                "--- Original\n+++ New\n@@ @@\n-abc\n+adc\n",
                'abc',
                'adc',
            ],
            [
                "--- Original\n+++ New\n@@ @@\n-ab\n+abc\n",
                'ab',
                'abc',
            ],
            [
                "--- Original\n+++ New\n@@ @@\n-bc\n+abc\n",
                'bc',
                'abc',
            ],
            [
                "--- Original\n+++ New\n@@ @@\n-abc\n+abbc\n",
                'abc',
                'abbc',
            ],
            [
                "--- Original\n+++ New\n@@ @@\n-abcdde\n+abcde\n",
                'abcdde',
                'abcde',
            ],
            [
                "--- Original\n+++ New\n@@ @@\n-a\t\n+b\t\n",
                "a\t",
                "b\t",
            ],
        ];
    }
}
