<?php
/*
 * This file is part of the Diff package.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace nochso\Diff;

use nochso\Omni\EOL;
use nochso\Omni\Multiline;
use nochso\Diff\LCS\LongestCommonSubsequence;
use nochso\Diff\LCS\TimeEfficientImplementation;
use nochso\Diff\LCS\MemoryEfficientImplementation;

/**
 * Diff implementation.
 */
class Differ
{
    const HEADER_DEFAULT = ['--- Original','+++ New'];

    /**
     * @var string
     */
    private $header;

    /**
     * @var bool
     */
    private $showNonDiffLines;

    /**
     * @param string[] $header
     * @param bool     $showNonDiffLines
     */
    public function __construct(array $header = self::HEADER_DEFAULT, $showNonDiffLines = true)
    {
        $this->header           = $header;
        $this->showNonDiffLines = $showNonDiffLines;
    }

    /**
     * Returns the diff between two arrays or strings as string.
     *
     * @param array|string             $from
     * @param array|string             $to
     * @param LongestCommonSubsequence $lcs
     *
     * @return string
     */
    public function diff($from, $to, LongestCommonSubsequence $lcs = null)
    {
        if (!is_array($from) && !is_string($from)) {
            $from = (string) $from;
        }

        if (!is_array($to) && !is_string($to)) {
            $to = (string) $to;
        }

        $lines  = new Multiline($this->header);
        $lines->setEol("\n");
        $diff   = $this->diffToArray($from, $to, $lcs);

        $inOld = false;
        $i     = 0;
        $old   = array();

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
        $end   = count($diff);

        if ($tmp = array_search($end, $old)) {
            $end = $tmp;
        }

        $newChunk = true;

        for ($i = $start; $i < $end; $i++) {
            if (isset($old[$i])) {
                $lines->add('');
                $newChunk = true;
                $i        = $old[$i];
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

        $lines->add('');

        return (string) $lines;
    }

    /**
     * Returns the diff between two arrays or strings as array.
     *
     * Each array element contains two elements:
     *   - [0] => string $token
     *   - [1] => 2|1|0
     *
     * - 2: REMOVED: $token was removed from $from
     * - 1: ADDED: $token was added to $from
     * - 0: OLD: $token is not changed in $to
     *
     * @param array|string             $from
     * @param array|string             $to
     * @param LongestCommonSubsequence $lcs
     *
     * @return array
     */
    public function diffToArray($from, $to, LongestCommonSubsequence $lcs = null)
    {
        $diff = array();
        $this->addLineEndingWarning($from, $to, $diff);

        if (is_string($from)) {
            $from = Multiline::create($from)->toArray();
        }

        if (is_string($to)) {
            $to = Multiline::create($to)->toArray();
        }

        $start      = array();
        $end        = array();
        $fromLength = count($from);
        $toLength   = count($to);
        $length     = min($fromLength, $toLength);

        for ($i = 0; $i < $length; ++$i) {
            if ($from[$i] === $to[$i]) {
                $start[] = $from[$i];
                unset($from[$i], $to[$i]);
            } else {
                break;
            }
        }

        $length -= $i;

        for ($i = 1; $i < $length; ++$i) {
            if ($from[$fromLength - $i] === $to[$toLength - $i]) {
                array_unshift($end, $from[$fromLength - $i]);
                unset($from[$fromLength - $i], $to[$toLength - $i]);
            } else {
                break;
            }
        }

        if ($lcs === null) {
            $lcs = $this->selectLcsImplementation($from, $to);
        }

        $common = $lcs->calculate(array_values($from), array_values($to));

        foreach ($start as $token) {
            $diff[] = array($token, 0 /* OLD */);
        }

        reset($from);
        reset($to);

        foreach ($common as $token) {
            while ((($fromToken = reset($from)) !== $token)) {
                $diff[] = array(array_shift($from), 2 /* REMOVED */);
            }

            while ((($toToken = reset($to)) !== $token)) {
                $diff[] = array(array_shift($to), 1 /* ADDED */);
            }

            $diff[] = array($token, 0 /* OLD */);

            array_shift($from);
            array_shift($to);
        }

        while (($token = array_shift($from)) !== null) {
            $diff[] = array($token, 2 /* REMOVED */);
        }

        while (($token = array_shift($to)) !== null) {
            $diff[] = array($token, 1 /* ADDED */);
        }

        foreach ($end as $token) {
            $diff[] = array($token, 0 /* OLD */);
        }

        return $diff;
    }

    /**
     * @param array $from
     * @param array $to
     *
     * @return LongestCommonSubsequence
     */
    private function selectLcsImplementation(array $from, array $to)
    {
        // We do not want to use the time-efficient implementation if its memory
        // footprint will probably exceed this value. Note that the footprint
        // calculation is only an estimation for the matrix and the LCS method
        // will typically allocate a bit more memory than this.
        $memoryLimit = 100 * 1024 * 1024;

        if ($this->calculateEstimatedFootprint($from, $to) > $memoryLimit) {
            return new MemoryEfficientImplementation;
        }

        return new TimeEfficientImplementation;
    }

    /**
     * Calculates the estimated memory footprint for the DP-based method.
     *
     * @param array $from
     * @param array $to
     *
     * @return int
     */
    private function calculateEstimatedFootprint(array $from, array $to)
    {
        $itemSize = PHP_INT_SIZE == 4 ? 76 : 144;

        return $itemSize * pow(min(count($from), count($to)), 2);
    }

    /**
     * @param string $from
     * @param string $to
     * @param array  $diff
     */
    private function addLineEndingWarning($from, $to, &$diff)
    {
        try {
            $fromEol = EOL::detect($from);
            $toEol   = EOL::detect($to);
        } catch (\Exception $e) {
            // Comparison is useless when no line endings are found.
            return;
        }
        if ((string) $fromEol === (string) $toEol) {
            return;
        }
        $warning = sprintf(
            '#Warning: Line ending changed from %s to %s',
            $fromEol->getName(),
            $toEol->getName()
        );
        $diff[]  = [$warning, 0];
    }
}
