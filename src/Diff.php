<?php
namespace nochso\Diff;

use nochso\Diff\LCS\LongestCommonSubsequence;
use nochso\Omni\EOL;

class Diff
{
    /**
     * @var mixed[][]
     */
    private $contextDiffLines;
    /**
     * @var \nochso\Diff\ContextDiff
     */
    private $context;
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
        $diff->context = $context;
        $diff->addLineEndingWarning($from, $to);
        $diff->contextDiffLines = $diff->context->create($fullDiffLines);
        return $diff;
    }

    /**
     * @return \Generator|\nochso\Diff\DiffLine[]
     */
    public function yieldDiffLines()
    {
        foreach ($this->contextDiffLines as $contextDiffLine) {
            yield new DiffLine($contextDiffLine);
        }
    }

    /**
     * @return \nochso\Diff\DiffLine[]
     */
    public function getDiffLines()
    {
        $lines = [];
        foreach ($this->contextDiffLines as $contextDiffLine) {
            $lines[] = new DiffLine($contextDiffLine);
        }
        return $lines;
    }

    /**
     * @return int
     */
    public function getMaxLineNumber()
    {
        $lastKey = count($this->contextDiffLines) - 1;
        return $this->contextDiffLines[$lastKey][DiffLine::LINE_NUMBER_FROM];
    }

    /**
     * @return string[]
     */
    public function getMessages()
    {
        return $this->messages;
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
