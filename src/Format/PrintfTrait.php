<?php
namespace nochso\Diff\Format;

use nochso\Diff\Differ;

/**
 * PrintfTrait for simple line formatting using printf() formats.
 *
 * `setLineCount()` must be called before using it in a template. `Differ` should do this for you by default.
 *
 * @todo Refactor this into an object "PrintfHelper"
 */
trait PrintfTrait
{
    protected $sameFormat = ' %s';
    protected $addFormat = '+%s';
    protected $removeFormat = '-%s';
    protected $lineNumberFormat = '%s:';

    /**
     * @param mixed[] $line
     *
     * @return string
     */
    public function formatLine($line)
    {
        $format = $this->sameFormat;
        if ($line[1] === Differ::ADD) {
            $format = $this->addFormat;
        } elseif ($line[1] === Differ::REMOVE) {
            $format = $this->removeFormat;
        }
        return sprintf($format, $line[0]);
    }

    /**
     * @param mixed[] $line
     *
     * @return string
     */
    public function formatLineNumber($line)
    {
        if ($line[1] !== Differ::ADD) {
            $paddedLineNumber = str_pad($line[2], $this->getLineCountLength(), ' ', STR_PAD_LEFT);
        } else {
            $paddedLineNumber = str_pad('', $this->getLineCountLength(), ' ', STR_PAD_LEFT);
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

    /**
     * @return int
     *
     * @todo Move this up to PhpTemplate?
     */
    private function getLineCountLength()
    {
        $diff = $this->getDiff();
        end($diff);
        $lastKey = key($diff);
        reset($diff);
        return strlen($lastKey);
    }
}
