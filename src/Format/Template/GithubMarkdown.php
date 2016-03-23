<?php
namespace nochso\Diff\Format\Template;

use nochso\Diff\Diff;
use nochso\Diff\DiffLine;
use nochso\Diff\Escape;

/**
 * GithubMarkdown.
 */
class GithubMarkdown extends PhpTemplate
{
    /**
     * @var bool
     */
    private $isLineNumberEnabled = true;
    /**
     * @var int
     */
    private $lineCountLength;

    public function __construct($path = 'GithubMarkdown.php', $basePath = __DIR__ . '/../../../template')
    {
        parent::__construct($path, $basePath);
        $this->setEscaper(new Escape\Html());
    }

    public function format(Diff $diff)
    {
        $this->lineCountLength = strlen($diff->getMaxLineNumber());
        return parent::format($diff);
    }

    public function formatLine(DiffLine $line)
    {
        $action = ' ';
        if ($line->isAddition()) {
            $action = '+';
        } elseif ($line->isRemoval()) {
            $action = '-';
        }
        if ($this->isLineNumberEnabled) {
            $format = $action . '%s: %s';
            return sprintf($format, $this->formatLineNumber($line), $line->getText());
        }
        $format = $action . '%s';
        return sprintf($format, $line->getText());
    }

    /**
     * @return $this
     */
    public function enableLineNumber()
    {
        $this->isLineNumberEnabled = true;
        return $this;
    }

    /**
     * @return $this
     */
    public function disableLineNumber()
    {
        $this->isLineNumberEnabled = false;
        return $this;
    }

    /**
     * @param \nochso\Diff\DiffLine $line
     *
     * @return string
     */
    private function formatLineNumber(DiffLine $line)
    {
        $number = '';
        if (!$line->isAddition()) {
            $number = $line->getLineNumberFrom();
        }
        return str_pad($number, $this->lineCountLength, ' ', STR_PAD_LEFT);
    }
}
