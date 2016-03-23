<?php
namespace nochso\Diff\Format\Template;

use nochso\Diff\ContextDiff;
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
        $gh = new GithubMarkdown();
        $output = $gh->format(Diff::create($from, $to));
        $this->assertSame($expected, $output);
    }

    public function formatWithoutLineNumberProvider()
    {
        return TestProvider::fromFile('githubmarkdown.without.linenumber.txt');
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
        $formatter = new GithubMarkdown();
        $formatter->disableLineNumber();
        $output = $formatter->format(Diff::create($from, $to));
        $this->assertSame($expected, $output);
    }

    public function contextProvider()
    {
        return TestProvider::fromFile('githubmarkdown.context.txt');
    }
    /**
     * @dataProvider contextProvider
     */
    public function testContext($maxContext, $from, $to, $expected)
    {
        $gh = new GithubMarkdown();
        $context = new ContextDiff();
        $context->setMaxContext($maxContext);
        $this->assertEquals($expected, $gh->format(Diff::create($from, $to, $context)));
    }
}
