<?php
namespace nochso\Diff\Format\Template;

use nochso\Diff\Diff;
use nochso\Diff\DiffLine;
use nochso\Diff\Format\Printf;

/**
 * POSIX.
 */
class POSIX extends PhpTemplate
{
    const RESET = 0;
    const STYLE_BOLD = 1;
    const STYLE_DIM = 2;
    const STYLE_UL = 4;
    const STYLE_BLINK = 5;
    const STYLE_REVERSE = 7;
    const FG_BLACK = 30;
    const FG_RED = 31;
    const FG_GREEN = 32;
    const FG_YELLOW = 33;
    const FG_BLUE = 34;
    const FG_MAGENTA = 35;
    const FG_CYAN = 36;
    const FG_WHITE = 37;
    const BLACKBG = 40;
    const BG_RED = 41;
    const BG_GREEN = 42;
    const BG_YELLOW = 43;
    const BG_BLUE = 44;
    const BG_MAGENTA = 45;
    const BG_CYAN = 46;
    const BG_WHITE = 47;

    /**
     * @var \nochso\Diff\Format\Printf
     */
    private $printf;

    public function __construct($path = 'Text.php', $basePath = __DIR__ . '/../../../template')
    {
        parent::__construct($path, $basePath);
        $this->printf = new Printf();
        $same = '%s';
        $add = $this->color('%s', self::FG_GREEN);
        $remove = $this->color('%s', self::FG_RED);
        $lineNumber = $this->color('%s', self::FG_YELLOW) . ' %s';
        $this->printf->setFormats($same, $add, $remove, $lineNumber);
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
     * @param $input
     * @param int|int[] $colors
     *
     * @return string
     */
    public function color($input, $colors)
    {
        if (!is_array($colors)) {
            $colors = [$colors];
        }
        $out = '';
        foreach ($colors as $color) {
            $out .= sprintf("\e[%sm", $color);
        }
        return sprintf("%s%s\e[%sm", $out, $input, self::RESET);
    }
}
