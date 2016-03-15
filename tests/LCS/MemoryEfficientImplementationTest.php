<?php

namespace nochso\Diff\LCS;

class MemoryEfficientImplementationTest extends ImplementationTest
{
    protected function setUp()
    {
        parent::setUp();
        $this->implementation = new MemoryEfficientImplementation();
    }
}
