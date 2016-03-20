<?php
namespace nochso\Diff\Format\Template;

use nochso\Diff\ContextDiff;
use nochso\Diff\Diff;
use nochso\Diff\Format\Formatter;
use nochso\Omni\Path;

/**
 * PhpTemplate is a base class writing more specific template formatters.
 *
 * Templates have access to this object via $this.
 *
 * Inherit from this class to add extra functionality.
 *
 * Get auto-completion by adding a PHPDoc hint at the top of the template using &#64;var $this \Class
 *
 * @todo Refactor me (ContextDiff)
 */
class PhpTemplate implements Formatter
{
    const MAX_CONTEXT_DEFAULT = 3;
    const ALL_CONTEXT = -1;

    /**
     * @var int
     */
    protected $maxContext = self::MAX_CONTEXT_DEFAULT;
    /**
     * @var \nochso\Diff\Diff
     */
    protected $diff;
    /**
     * @var bool
     */
    protected $showLineNumber = false;
    /**
     * @var string
     */
    private $basePath;
    /**
     * @var string
     */
    private $path;
    /**
     * @var \nochso\Diff\Format\Escaper
     */
    private $escaper;
    /**
     * @var \nochso\Diff\ContextDiff
     */
    private $contextDiff;

    public function __construct($path, $basePath = __DIR__ . '/../../../template')
    {
        $this->path = $path;
        $this->basePath = $basePath;
        $this->contextDiff = new ContextDiff();
    }

    /**
     * Format an array diff by using plain PHP templates.
     *
     * @param \nochso\Diff\Diff $diff
     *
     * @return mixed
     */
    public function format(Diff $diff)
    {
        $this->diff = $diff;
        ob_start();
        include Path::combine($this->basePath, $this->path);
        return ob_get_clean();
    }

    /**
     * showLineNumber based on the from/before string.
     *
     * @param bool $showLineNumber
     */
    public function showLineNumber($showLineNumber = true)
    {
        $this->showLineNumber = $showLineNumber;
    }

    /**
     * @return bool
     */
    public function isShowingLineNumber()
    {
        return $this->showLineNumber;
    }

    public function getMessages()
    {
        return $this->diff->getMessages();
    }

    /**
     * @return \nochso\Diff\Diff
     */
    public function getDiff()
    {
        return $this->diff;
    }

    /**
     * @param callable|null $callable
     */
    public function setEscaper($callable = null)
    {
        $this->escaper = $callable;
    }

    /**
     * @param string $input
     *
     * @return string
     */
    public function escape($input)
    {
        if ($this->escaper === null) {
            return $input;
        }
        return $this->escaper->escape($input);
    }
}
