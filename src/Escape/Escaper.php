<?php
namespace nochso\Diff\Escape;

interface Escaper
{
    /**
     * @param string $input
     *
     * @return mixed
     */
    public function escape($input);
}
