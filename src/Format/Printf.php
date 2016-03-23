<?php
namespace nochso\Diff\Format;

use nochso\Diff\Diff;
use nochso\Diff\DiffLine;

/**
 * Printf helper for basic formatting of DiffLine objects.
 *
 * @link http://php.net/manual/en/function.sprintf.php
 */
class Printf
{
    private $sameFormat = ' %s';
    private $addFormat = '+%s';
    private $removeFormat = '-%s';
    /**
     * - %s Result from one of the first 3 formats.
     */
    private $lineFormat = '%s';
    /**
     * - %s Padded line number.
     * - %s Result from one of the first 3 formats.
     */
    private $lineNumberFormat = '%s: %s';
    /**
     * @var bool
     */
    private $isLineNumberEnabled = true;
    /**
     * @var int
     */
    private $lineCountLength;

    /**
     * setFormats has only optional parameters.
     *
     * Null parameters will use the default format so you can omit what you don't need.
     *
     * @param string $same
     * @param string $add
     * @param string $remove
     * @param string $lineNumber
     */
    public function setFormats($same = null, $add = null, $remove = null, $lineNumber = null)
    {
        if ($same !== null) {
            $this->sameFormat = $same;
        }
        if ($add !== null) {
            $this->addFormat = $add;
        }
        if ($remove !== null) {
            $this->removeFormat = $remove;
        }
        if ($lineNumber !== null) {
            $this->lineNumberFormat = $lineNumber;
        }
    }

    /**
     * Prepare this helper before first using it to format lines.
     *
     * @param \nochso\Diff\Diff $diff
     */
    public function prepare(Diff $diff)
    {
        $this->lineCountLength = strlen($diff->getMaxLineNumber());
    }

    /**
     * formatLine returns a complete line based on current the printf formats.
     *
     * @param \nochso\Diff\DiffLine $line
     *
     * @return string
     */
    public function formatLine(DiffLine $line)
    {
        $text = $this->formatText($line);
        if ($this->isLineNumberEnabled) {
            return sprintf($this->lineNumberFormat, $this->formatLineNumber($line), $text);
        }
        return sprintf($this->lineFormat, $text);
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
    public function formatText(DiffLine $line)
    {
        $format = $this->sameFormat;
        if ($line->isAddition()) {
            $format = $this->addFormat;
        } elseif ($line->isRemoval()) {
            $format = $this->removeFormat;
        }
        $text = sprintf($format, $line->getText());
        return $text;
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
