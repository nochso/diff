<?php
namespace nochso\Diff\Format;

use nochso\Diff\Diff;

interface Formatter
{
    /**
     * Format a Diff object.
     *
     * @param \nochso\Diff\Diff $diff
     *
     * @return mixed
     */
    public function format(Diff $diff);
}
