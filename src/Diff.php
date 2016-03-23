<?php
namespace nochso\Diff;

use nochso\Diff\Escape\Escaper;
use nochso\Diff\LCS\LongestCommonSubsequence;
use nochso\Omni\EOL;

class Diff
{
    /**
     * @var \nochso\Diff\DiffLine[]
     */
    private $diffLines = [];
    /**
     * @var string[]
     */
    private $messages = [];

    /**
     * @param string                                         $from
     * @param string                                         $to
     * @param \nochso\Diff\ContextDiff                       $context
     * @param \nochso\Diff\LCS\LongestCommonSubsequence|null $lcs
     *
     * @return \nochso\Diff\Diff
     */
    public static function create($from, $to, ContextDiff $context = null, LongestCommonSubsequence $lcs = null)
    {
        $diff = new self();
        $differ = new Differ();
        $fullDiffLines = $differ->diffToArray($from, $to, $lcs);
        if ($context === null) {
            $context = new ContextDiff();
        }
        $diff->addLineEndingWarning($from, $to);
        $contextDiffLines = $context->create($fullDiffLines);
        foreach ($contextDiffLines as $contextDiffLine) {
            $diff->diffLines[] = new DiffLine($contextDiffLine);
        }
        return $diff;
    }

    /**
     * @return \nochso\Diff\DiffLine[]
     */
    public function getDiffLines()
    {
        return $this->diffLines;
    }

    /**
     * @return int
     */
    public function getMaxLineNumber()
    {
        $count = count($this->diffLines);
        if ($count === 0) {
            return 0;
        }
        $lastKey = $count - 1;
        return $this->diffLines[$lastKey]->getLineNumberFrom();
    }

    /**
     * @return string[]
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * escapeText of all DiffLine objects.
     *
     * @param \nochso\Diff\Escape\Escaper $escaper
     */
    public function escapeText(Escaper $escaper = null)
    {
        if ($escaper === null) {
            return;
        }
        foreach ($this->diffLines as $line) {
            $line->setText($escaper->escape($line->getText()));
        }
    }

    /**
     * @param string $from
     * @param string $to
     */
    private function addLineEndingWarning($from, $to)
    {
        try {
            $fromEol = EOL::detect($from);
            $toEol = EOL::detect($to);
        } catch (\Exception $e) {
            // Comparison is useless when no line endings are found.
            return;
        }
        if ((string) $fromEol === (string) $toEol) {
            return;
        }
        $warning = sprintf(
            'Warning: Line ending changed from %s to %s',
            $fromEol->getName(),
            $toEol->getName()
        );
        $this->messages[] = $warning;
    }
}
