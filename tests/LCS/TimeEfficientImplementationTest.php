<?php

namespace nochso\Diff\LCS;

/**
 * Some of these tests are volontary stressfull, in order to give some approximative benchmark hints.
 */
class TimeEfficientImplementationTest extends ImplementationTest
{
    protected function setUp()
    {
        parent::setUp();
        $this->implementation = new TimeEfficientImplementation;
    }
}
