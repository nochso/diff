<?php
namespace nochso\Diff;

use nochso\Omni\Path;

class TestProvider
{
    /**
     * fromFile returns tests from a file and requires that the first section is a name/description of the dataset.
     *
     * @param string $path
     * @param string $testSeparator
     * @param string $parameterSeparator
     *
     * @return array
     */
    public static function fromFile($path, $testSeparator = '###', $parameterSeparator = '===')
    {
        $paramSplitter = '/\n?' . $parameterSeparator . '\n?/';
        $testSplitter = '/\n?' . $testSeparator . '\n?/';
        $path = Path::combine(__DIR__ . '/fixtures', $path);
        $rawTests = preg_split($testSplitter, file_get_contents($path));
        $tests = [];
        $testName = pathinfo($path, PATHINFO_FILENAME);
        foreach ($rawTests as $rawTest) {
            $params = preg_split($paramSplitter, $rawTest);
            $tests[$testName . ' ' . $params[0]] = array_slice($params, 1);
        }
        return $tests;
    }
}
