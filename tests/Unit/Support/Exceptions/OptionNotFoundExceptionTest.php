<?php

namespace Tests\Unit\Support\Exceptions;

use PHPUnit\Framework\TestCase;
use Cruftman\Support\Exceptions\OptionNotFoundException;

class OptionNotFoundExceptionTest extends TestCase
{
    /**
     * Ensure that class OptionNotFoundException extends \Exception.
     *
     * @return void
     */
    public function testExtendsException()
    {
        $this->assertContains(\Exception::class,class_parents(OptionNotFoundException::class));
    }
}
