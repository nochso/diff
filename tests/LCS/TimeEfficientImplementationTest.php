<?php
namespace nochso\Diff\LCS;

class TimeEfficientImplementationTest extends ImplementationTest
{
    protected function setUp()
    {
        parent::setUp();
        $this->implementation = new TimeEfficientImplementation();
    }
}
