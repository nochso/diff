<?php
namespace nochso\Diff;

/**
 * ContextDiff.
 *
 * @todo Refactor parts of PhpTemplate into this
 */
class ContextDiff
{
    const MAX_CONTEXT_DEFAULT = 3;
    const ALL_CONTEXT = -1;

    /**
     * @var int
     */
    private $maxContext = self::MAX_CONTEXT_DEFAULT;

    /**
     * parseFullDiff trims a full diff down to surround context lines.
     *
     * @param mixed[][] $fullDiff The result of Differ:diffToArray
     *
     * @return mixed[][] A trimmed down version of the original array with before-based line-numbers at index 2.
     *
     * @see Differ:diffToArray
     */
    public function create($fullDiff)
    {
        $this->addLineNumbersToFullDiff($fullDiff);
        if ($this->maxContext === self::ALL_CONTEXT) {
            return $fullDiff;
        }
        $changeRanges = $this->getChangeRanges($fullDiff);
        // List of line positions to keep
        $keepers = $this->getIndexesToKeep($fullDiff, $changeRanges);
        $contextDiff = [];
        foreach (array_keys($keepers) as $position => $key) {
            $contextDiff[] = $fullDiff[$key];
        }
        return $contextDiff;
    }

    /**
     * getChangeRanges returns the start and end positions of grouped changes.
     *
     * Each element consists of the start and end position:
     *
     * ```
     * 0 => [5, 10]
     * 1 => [14, 14]
     * ```
     *
     * @param mixed[][] $fullDiff
     *
     * @return array
     */
    public function getChangeRanges($fullDiff)
    {
        $ranges = [];
        $start = null;
        $end = null;
        foreach ($fullDiff as $key => $line) {
            if ($line[DiffLine::ACTION] === Differ::SAME) {
                // If there has been a change, end it here
                if ($start !== null) {
                    $ranges[] = [$start, $end];
                }
                // Remember we're not within a change.
                $start = null;
                $end = null;
            } else {
                // Mark chunk start and keep updating the end
                if ($start === null) {
                    $start = $key;
                }
                $end = $key;
            }
        }
        if ($start !== null) {
            $ranges[] = [$start, $end];
        }
        return $ranges;
    }

    /**
     * Set the maximum amount of context lines surrounding changes.
     *
     * If set to zero, only changed lines are returned.
     *
     * See the class constants:
     * - Template::ALL_CONTEXT - Show all lines.
     * - Template::MAX_CONTEXT_DEFAULT - Show a maximum of 3 context lines.
     *
     * @param int $maxContext
     */
    public function setMaxContext($maxContext)
    {
        $this->maxContext = (int) $maxContext;
    }

    private function addLineNumbersToFullDiff(&$fullDiff)
    {
        $lineNumber = 0;
        foreach ($fullDiff as $key => $line) {
            if ($line[DiffLine::ACTION] !== Differ::ADD) {
                ++$lineNumber;
            }
            $fullDiff[$key][DiffLine::LINE_NUMBER_FROM] = $lineNumber;
        }
    }

    /**
     * getIndexesToKeep returns a map of diff index keys that are to be kept based on the output of getChangeRanges().
     *
     * @param $fullDiff
     * @param $changeRanges
     *
     * @return array Key: Index in diff array. Value: Always true.
     */
    private function getIndexesToKeep($fullDiff, $changeRanges)
    {
        $keepers = [];
        $diffCount = count($fullDiff);
        foreach ($changeRanges as $changeRange) {
            // Prevent lower and upper out of bounds access
            $contextStart = max(0, $changeRange[0] - $this->maxContext);
            $contextEnd = min($diffCount - 1, $changeRange[1] + $this->maxContext);
            for ($i = $contextStart; $i <= $contextEnd; $i++) {
                $keepers[$i] = true;
            }
        }
        return $keepers;
    }
}
