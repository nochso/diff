<?php
namespace nochso\Diff;

/**
 * DiffLine holds a single line of a diff.
 */
class DiffLine
{
    // Basic items as returned by Differ::diffToArray
    const TEXT = 0;
    const ACTION = 1;
    // Additional info provided by ContextDiff
    const LINE_NUMBER_FROM = 2;

    /**
     * @var array
     */
    private $line;

    /**
     * @return int
     */
    public function getLineNumberFrom()
    {
        return $this->line[self::LINE_NUMBER_FROM];
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->line[self::TEXT];
    }

    /**
     * @return bool
     */
    public function isAddition()
    {
        return $this->line[self::ACTION] === Differ::ADD;
    }

    /**
     * @return bool
     */
    public function isSame()
    {
        return $this->line[self::ACTION] === Differ::SAME;
    }

    /**
     * @return bool
     */
    public function isRemoval()
    {
        return $this->line[self::ACTION] === Differ::REMOVE;
    }

    /**
     * @param mixed[] $diffLine
     */
    public function __construct($diffLine)
    {
        $this->line = $diffLine;
    }
}
