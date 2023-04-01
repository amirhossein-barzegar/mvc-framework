<?php

namespace Test;

use PHPUnit\Framework\TestCase;

class ExampleAssertionsTest extends TestCase
{
    public function testMessageStartWithHello()
    {
        $this->assertStringStartsWith('Hello', 'Hello World');
    }
}