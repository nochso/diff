<?php
namespace nochso\Diff;

/**
 * ContextDiff trims full diffs down to differing lines and surrounding context.
 */
class ContextDiff
{
    /**
     * Default maximum amount of lines surrounding modified lines.
     */
    const MAX_CONTEXT_DEFAULT = 3;
    /**
     * If max context is set to this, all lines are kept.
     */
    const ALL_CONTEXT = -1;

    /**
     * @var int
     */
    private $maxContext = self::MAX_CONTEXT_DEFAULT;
    /**
     * @var mixed[][]
     */
    private $fullDiff;

    /**
     * Create a trimmed diff with a configurable context surrounding changes.
     *
     * @param mixed[][] $fullDiff The result of Differ::diffToArray
     *
     * @return mixed[][] A trimmed down version of the original array with before-based line-numbers at index 2.
     *
     * @see Differ:diffToArray
     */
    public function create($fullDiff)
    {
        $contextDiff = [];
        $this->fullDiff = $fullDiff;
        $this->addLineNumbersToFullDiff();
        if ($this->maxContext === self::ALL_CONTEXT) {
            return $this->fullDiff;
        }
        // List of line positions to keep
        $keepers = $this->getIndexesToKeep();
        foreach (array_keys($keepers) as $position => $key) {
            $contextDiff[] = $this->fullDiff[$key];
        }
        return $contextDiff;
    }

    /**
     * extractChangeRanges returns the start and end positions of grouped changes.
     *
     * Each element consists of the start and end position:
     *
     * ```
     * 0 => [5, 10]
     * 1 => [14, 14]
     * ```
     *
     * @param mixed[][] $fullDiff The result of Differ::diffToArray
     *
     * @return array
     */
    public function extractChangeRanges($fullDiff)
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
     * See the constants of this class for predefined values.
     *
     * @param int $maxContext Any non-negative integer or one of the class constants.
     */
    public function setMaxContext($maxContext)
    {
        $this->maxContext = (int) $maxContext;
    }

    private function addLineNumbersToFullDiff()
    {
        $lineNumber = 0;
        foreach ($this->fullDiff as $key => $line) {
            if ($line[DiffLine::ACTION] !== Differ::ADD) {
                ++$lineNumber;
            }
            $this->fullDiff[$key][DiffLine::LINE_NUMBER_FROM] = $lineNumber;
        }
    }

    /**
     * getIndexesToKeep returns a map of diff index keys that are to be kept based on the output of getChangeRanges().
     *
     * @return array Key: Index in diff array. Value: Always true.
     */
    private function getIndexesToKeep()
    {
        $keepers = [];
        $diffCount = count($this->fullDiff);
        $changeRanges = $this->extractChangeRanges($this->fullDiff);
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
