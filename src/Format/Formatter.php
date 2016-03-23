<?php
namespace nochso\Diff\Format;

use nochso\Diff\Diff;

/**
 * Formatter interface implemented by templates and anything that can render a Diff.
 */
interface Formatter
{
    /**
     * Format a Diff object.
     *
     * @param \nochso\Diff\Diff $diff The diff to be formatted / rendered.
     *
     * @return mixed
     */
    public function format(Diff $diff);
}
