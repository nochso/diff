<?php
namespace nochso\Diff\Format\Template;

use nochso\Diff\Diff;
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
        $output = (new GithubMarkdown())->format(Diff::create($from, $to));
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
        $gh = new GithubMarkdown();
        $gh->showLineNumber();
        $output = $gh->format(Diff::create($from, $to));
        $this->assertSame($expected, $output);
    }
}
