<?php
namespace nochso\Diff\Escape;

use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;

/**
 * CliEscaper escapes control characters using Symfony's CliDumper.
 */
class CliEscaper implements Escaper
{
    /**
     * @var \Symfony\Component\VarDumper\Cloner\VarCloner
     */
    private $cloner;
    /**
     * @var \Symfony\Component\VarDumper\Dumper\CliDumper
     */
    private $dumper;
    /**
     * @var resource
     */
    private $memoryStream;

    public function __construct()
    {
        $this->cloner = new VarCloner();
        $this->dumper = new CliDumper();
        $this->memoryStream = fopen('php://memory', 'r+b');
    }

    /**
     * @param string $input
     *
     * @return mixed
     */
    public function escape($input)
    {
        $this->dumper->dump($this->cloner->cloneVar($input), $this->memoryStream);
        $output = stream_get_contents($this->memoryStream, -1, 0);
        ftruncate($this->memoryStream, 0);
        return rtrim($output, "\n");
    }

    public function __destruct()
    {
        fclose($this->memoryStream);
    }
}
