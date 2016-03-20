<?php
namespace nochso\Diff\Format\Template;

use nochso\Diff\Format\PrintfTrait;

class Text extends PhpTemplate
{
    use PrintfTrait;

    public function __construct($path = 'Text.php', $basePath = __DIR__ . '/../../../template')
    {
        parent::__construct($path, $basePath);
        $this->setPrintfFormats(null, null, null, '%s: ');
    }
}
