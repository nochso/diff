<?php
namespace nochso\Diff\Format\Template;

use nochso\Diff\Differ;
use nochso\Diff\DiffLine;
use nochso\Diff\Format\HTMLEscaper;
use nochso\Diff\Format\PrintfTrait;

/**
 * GithubMarkdown.
 */
class GithubMarkdown extends PhpTemplate
{
    use PrintfTrait;

    public function __construct($path = 'GithubMarkdown.php', $basePath = __DIR__ . '/../../../template')
    {
        parent::__construct($path, $basePath);
        $this->setPrintfFormats(null, null, null, '%s: ');
        $this->setEscaper(new HTMLEscaper());
    }

    /**
     * @param mixed[] $line
     *
     * @return string
     */
    public function formatLine($line)
    {
        $format = $this->sameFormat;
        if ($line[DiffLine::ACTION] === Differ::ADD) {
            $format = $this->addFormat;
        } elseif ($line[DiffLine::ACTION] === Differ::REMOVE) {
            $format = $this->removeFormat;
        }
        if ($this->isShowingLineNumber()) {
            return sprintf($format, sprintf($this->formatLineNumber($line)) . $line[0]);
        } else {
            return sprintf($format, $line[0]);
        }
    }
}
