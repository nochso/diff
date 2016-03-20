<?php
namespace nochso\Diff\Format\Template;

use nochso\Diff\ContextDiff;
use nochso\Diff\Differ;
use nochso\Diff\DiffLine;
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
     * diff lines as prepared by this class.
     *
     * @var mixed[][]
     */
    protected $diff;
    /**
     * fullDiff lines as provided by Differ.
     *
     * @var mixed[][]
     */
    protected $fullDiff;
    /**
     * @var string[]
     */
    protected $messages;
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
     * @var ContextDiff
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
     * @param mixed[][] $diff     The result of Differ::diffToArray
     * @param string[]  $messages Optional array of messages or warnings.
     *
     * @return mixed
     */
    public function format($diff, $messages = [])
    {
        $this->fullDiff = $diff;
        $this->diff = null;
        $this->messages = $messages;
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
        return $this->messages;
    }

    public function getDiff()
    {
        if ($this->diff === null) {
            $this->prepareDiff();
        }
        return $this->diff;
    }

    /**
     * getDiffWithCommonContext returns added/removed lines with a maximum context of surrounding common lines.
     *
     * @see setMaxContext()
     *
     * @return mixed[][]
     */
    public function prepareDiff()
    {
        $this->diff = $this->contextDiff->create($this->fullDiff);
        $this->escapeLines();
    }

    /**
     * @return ContextDiff
     */
    public function getContextDiff()
    {
        return $this->contextDiff;
    }

    /**
     * @param ContextDiff $contextDiff
     */
    public function setContextDiff($contextDiff)
    {
        $this->contextDiff = $contextDiff;
    }

    /**
     * @param callable $callable
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

    private function escapeLines()
    {
        if ($this->escaper !== null) {
            foreach ($this->diff as &$line) {
                $line[DiffLine::TEXT] = $this->escape($line[DiffLine::TEXT]);
            }
        }
    }
}
