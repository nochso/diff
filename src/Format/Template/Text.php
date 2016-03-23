<?php
namespace nochso\Diff\Format\Template;

use nochso\Diff\Diff;
use nochso\Diff\DiffLine;
use nochso\Diff\Format\Printf;

class Text extends PhpTemplate
{
    /**
     * @var \nochso\Diff\Format\Printf
     */
    private $printf;

    public function __construct($path = 'Text.php', $basePath = __DIR__ . '/../../../template')
    {
        parent::__construct($path, $basePath);
        $this->printf = new Printf();
    }

    public function format(Diff $diff)
    {
        $this->printf->prepare($diff);
        return parent::format($diff);
    }

    public function printfLine(DiffLine $line)
    {
        return $this->printf->formatLine($line);
    }
}
