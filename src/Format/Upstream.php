<?php
namespace nochso\Diff\Format;

use nochso\Diff\Diff;
use nochso\Omni\Multiline;

/**
 * UpstreamFormatter tries to mirror the default behaviour of sebastianbergmann/diff.
 */
class Upstream implements Formatter
{
    const HEADER_DEFAULT = ['--- Original', '+++ New'];

    private $showNonDiffLines = true;
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
     * @param string[] $headerLines
     */
    public function setHeader(array $headerLines)
    {
        $this->header = $headerLines;
    }

    public function format(Diff $diff)
    {
        $messages = new Multiline($diff->getMessages());
        $messages->prefix('#');
        $lines = new Multiline(array_merge($this->header, $messages->toArray()));
        $lines->setEol("\n");
        $inOld = false;
        $i = 0;
        $old = [];
        $diffLines = $diff->getDiffLines();
        foreach ($diffLines as $line) {
            if ($line->isSame()) {
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
        $end = count($diffLines);
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
            if ($diffLines[$i]->isAddition()) {
                $lines->add('+' . $diffLines[$i]->getText());
            } elseif ($diffLines[$i]->isRemoval()) {
                $lines->add('-' . $diffLines[$i]->getText());
            } elseif ($this->showNonDiffLines === true) {
                $lines->add(' ' . $diffLines[$i]->getText());
            }
        }
        $lines->add('');
        return (string) $lines;
    }
}
