<?php
namespace nochso\Diff\Format\Template;

use nochso\Diff\Differ;
use nochso\Diff\TestProvider;

class GithubMarkdownTest extends \PHPUnit_Framework_TestCase
{
    public function formatProvider()
    {
        return TestProvider::fromFile('githubmarkdown.txt');
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
        $output = $diff->diff($from, $to, null, new GithubMarkdown());
        $this->assertSame($expected, $output);
    }

    public function showLineNumberProvider()
    {
        return TestProvider::fromFile('githubmarkdown.linenumber.txt');
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
        $diff = new Differ();
        $formatter = new GithubMarkdown();
        $formatter->showLineNumber();
        $output = $diff->diff($from, $to, null, $formatter);
        $this->assertSame($expected, $output);
    }
}
