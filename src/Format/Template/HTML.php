<?php
namespace nochso\Diff\Format\Template;

use nochso\Diff\Escape;
use nochso\Diff\Format\PrintfTrait;

class HTML extends PhpTemplate
{
    use PrintfTrait;

    public function __construct($path = 'HTML.php', $basePath = __DIR__ . '/../../../template')
    {
        parent::__construct($path, $basePath);
        $this->setPrintfFormats('%s', '<ins>%s</ins>', '<del>%s</del>');
        $this->setEscaper(new Escape\Html());
    }
}
