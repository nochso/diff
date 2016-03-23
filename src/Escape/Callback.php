<?php
namespace nochso\Diff\Escape;

/**
 * Callback turns a callable into an Escaper.
 *
 * Example using an existing function:
 * ```php
 * $esc = new Callback('str_rot13');
 * ```
 *
 * Example using a closure:
 * ```php
 * $esc = new Callback(function ($input) {
 *     $output = doSomethingWith($input);
 *     return $output;
 * });
 * ```
 *
 * @link http://php.net/manual/en/language.types.callable.php
 */
class Callback implements Escaper
{
    /**
     * @var callable
     */
    private $callable;

    /**
     * @param callable $callable
     */
    public function __construct(callable $callable)
    {
        $this->callable = $callable;
    }

    /**
     * @param string $input
     *
     * @return mixed
     */
    public function escape($input)
    {
        $callable = $this->callable;
        return $callable($input);
    }
}
