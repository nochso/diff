<?php
namespace nochso\Diff;

use nochso\Diff\Escape\Escaper;
use nochso\Diff\LCS\LongestCommonSubsequence;
use nochso\Omni\EOL;

/**
 * Diff consumes two strings and provides the resulting DiffLine objects.
 *
 * - Control how much context is kept by passing your own `ContextDiff` object.
 * - Check for potential messages or warnings via `getMessages()`. This is
 *   currently limited to a warning about line ending conflicts.
 */
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
     * Create a new Diff from two strings.
     *
     * @param string                                         $from    From/before string.
     * @param string                                         $to      To/after string.
     * @param \nochso\Diff\ContextDiff|null                  $context Optional ContextDiff to control the surrounding
     *                                                                context lines. If null, a default ContextDiff
     *                                                                is used.
     * @param \nochso\Diff\LCS\LongestCommonSubsequence|null $lcs     Optional LCS implementation to use. If null, an
     *                                                                appropiate implementation will be chosen automatically.
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
     * @return \nochso\Diff\DiffLine[] List of DiffLine objects.
     */
    public function getDiffLines()
    {
        return $this->diffLines;
    }

    /**
     * @return int The highest line number present in the diff. This can be
     *             lower than the initial input that was provided.
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
     * @return string[] A list of messages or warnings, e.g. a detected line
     *                  ending conflict
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
