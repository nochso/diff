<?php
namespace nochso\Diff\Format\Template;

use nochso\Diff\Diff;
use nochso\Diff\DiffLine;
use nochso\Diff\Escape;
use nochso\Diff\Format\Printf;

class HTML extends PhpTemplate
{
    /**
     * @var \nochso\Diff\Format\Printf
     */
    private $printf;

    public function __construct($path = 'HTML.php', $basePath = __DIR__ . '/../../../template')
    {
        parent::__construct($path, $basePath);
        $this->setEscaper(new Escape\Html());
        $this->printf = new Printf();
        $this->printf->setFormats('%s', '<ins>%s</ins>', '<del>%s</del>');
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

    /**
     * @return \nochso\Diff\Format\Printf
     */
    public function getPrintf()
    {
        return $this->printf;
    }
}
