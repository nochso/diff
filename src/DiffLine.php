<?php
namespace nochso\Diff;

/**
 * DiffLine holds constants to index positions in a diff line.
 *
 * @todo Make this a usable object for templates and a common API? See ContextDiff.
 */
class DiffLine
{
    // Basic items as returned by Differ::diffToArray
    const TEXT = 0;
    const ACTION = 1;

    // Additional info provided by ContextDiff
    const LINE_NUMBER_FROM = 2;

    private function __construct()
    {
    }
}
