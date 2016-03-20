<?php
namespace nochso\Diff\Format;

interface Escaper
{
    /**
     * @param string $input
     *
     * @return mixed
     */
    public function escape($input);
}
