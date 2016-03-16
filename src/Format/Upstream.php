<?php
namespace nochso\Diff\Format;

use nochso\Omni\Multiline;
use nochso\Omni\Strings;

/**
 * UpstreamFormatter tries to mirror the default behaviour of sebastianbergmann/diff.
 */
class Upstream implements Formatter
{
    const HEADER_DEFAULT = ['--- Original', '+++ New'];

    private $showNonDiffLines = true;
    private $escapeControlChars = false;
    private $header = self::HEADER_DEFAULT;

    /**
     * @param bool $enable
     *
     * @return $this
     */
    public function showNonDiffLines($enable = true)
    {
        $this->showNonDiffLines = $enable;
        return $this;
    }

    /**
     * @param bool $enable
     *
     * @return $this
     */
    public function escapeControlChars($enable = true)
    {
        $this->escapeControlChars = $enable;
        return $this;
    }

    /**
     * @param string[] $headerLines
     */
    public function setHeader(array $headerLines)
    {
        $this->header = $headerLines;
    }

    public function format($diff, $messages = [])
    {
        $lines = new Multiline(array_merge($this->header, $messages));
        $lines->setEol("\n");
        $inOld = false;
        $i = 0;
        $old = [];
        foreach ($diff as $line) {
            if ($line[1] ===  0 /* OLD */) {
                if ($inOld === false) {
                    $inOld = $i;
                }
            } elseif ($inOld !== false) {
                if (($i - $inOld) > 5) {
                    $old[$inOld] = $i - 1;
                }
                $inOld = false;
            }

            ++$i;
        }
        $start = isset($old[0]) ? $old[0] : 0;
        $end = count($diff);
        if ($tmp = array_search($end, $old)) {
            $end = $tmp;
        }
        $newChunk = true;
        for ($i = $start; $i < $end; ++$i) {
            if (isset($old[$i])) {
                $lines->add('');
                $newChunk = true;
                $i = $old[$i];
            }
            if ($newChunk) {
                if ($this->showNonDiffLines === true) {
                    $lines->add('@@ @@');
                }
                $newChunk = false;
            }
            if ($diff[$i][1] === 1 /* ADDED */) {
                $lines->add('+' . $diff[$i][0]);
            } elseif ($diff[$i][1] === 2 /* REMOVED */) {
                $lines->add('-' . $diff[$i][0]);
            } elseif ($this->showNonDiffLines === true) {
                $lines->add(' ' . $diff[$i][0]);
            }
        }
        if ($this->escapeControlChars) {
            $lines->apply(function ($line) {
                return Strings::escapeControlChars($line);
            });
        }
        $lines->add('');
        return (string) $lines;
    }
}
