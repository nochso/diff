<?php
namespace nochso\Diff\Escape;

/**
 * Html escapes HTML using UTF-8 by default.
 */
class Html implements Escaper
{
    const DEFAULT_CHARSET = 'UTF-8';

    /**
     * @var string
     */
    private $charset;

    public function __construct($charset = self::DEFAULT_CHARSET)
    {
        $this->setCharset($charset);
    }

    /**
     * @param $input
     *
     * @return string
     */
    public function escape($input)
    {
        return htmlspecialchars($input, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    /**
     * @param string $charset
     */
    public function setCharset($charset)
    {
        $this->charset = $charset;
    }
}
