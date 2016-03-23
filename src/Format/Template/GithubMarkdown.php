<?php
namespace nochso\Diff\Format\Template;

use nochso\Diff\DiffLine;
use nochso\Diff\Escape;
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
        $this->setEscaper(new Escape\HTMLEscaper());
    }

    /**
     * @param \nochso\Diff\DiffLine $line
     *
     * @return string
     */
    public function formatLine(DiffLine $line)
    {
        $format = $this->sameFormat;
        if ($line->isAddition()) {
            $format = $this->addFormat;
        } elseif ($line->isRemoval()) {
            $format = $this->removeFormat;
        }
        $escapedText = $this->escape($line->getText());
        if ($this->isShowingLineNumber()) {
            return sprintf($format, sprintf($this->formatLineNumber($line)) . $escapedText);
        } else {
            return sprintf($format, $escapedText);
        }
    }
}
