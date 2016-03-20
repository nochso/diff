<?php
namespace nochso\Diff\Format;

use nochso\Diff\DiffLine;

/**
 * PrintfTrait for simple line formatting using printf() formats.
 *
 * `setLineCount()` must be called before using it in a template. `Differ` should do this for you by default.
 *
 * @todo Refactor this into an object "PrintfHelper" or into PhpTemplate
 */
trait PrintfTrait
{
    protected $sameFormat = ' %s';
    protected $addFormat = '+%s';
    protected $removeFormat = '-%s';
    protected $lineNumberFormat = '%s:';

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
        return sprintf($format, $this->escape($line->getText()));
    }

    /**
     * @param \nochso\Diff\DiffLine $line
     *
     * @return string
     */
    public function formatLineNumber(DiffLine $line)
    {
        $lineCountLength = strlen($this->getDiff()->getMaxLineNumber());
        if (!$line->isAddition()) {
            $paddedLineNumber = str_pad($line->getLineNumberFrom(), $lineCountLength, ' ', STR_PAD_LEFT);
        } else {
            $paddedLineNumber = str_pad('', $lineCountLength, ' ', STR_PAD_LEFT);
        }
        $foo = sprintf($this->lineNumberFormat, $paddedLineNumber);
        return $foo;
    }

    /**
     * setPrintfFormats has only optional parameters.
     *
     * Null parameters will use the default format so you can omit what you don't need.
     *
     * @param string $same
     * @param string $add
     * @param string $remove
     * @param string $lineNumber
     */
    public function setPrintfFormats($same = null, $add = null, $remove = null, $lineNumber = null)
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
}
