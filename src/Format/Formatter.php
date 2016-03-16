<?php
namespace nochso\Diff\Format;

interface Formatter
{
    /**
     * Format an array diff.
     *
     * @param mixed[][] $diff     The result of Differ::diffToArray
     * @param string[]  $messages Optional array of messages or warnings.
     *
     * @return mixed
     */
    public function format($diff, $messages = []);
}
