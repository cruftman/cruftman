<?php

namespace Tests\Unit\Support\Exceptions;

use PHPUnit\Framework\TestCase;
use Cruftman\Support\Exceptions\TemplateArrayException;

class TemplateArrayExceptionTest extends TestCase
{
    /**
     * Ensure that class TemplateArrayException extends \Exception.
     *
     * @return void
     */
    public function testExtendsException()
    {
        $this->assertContains(\Exception::class,class_parents(TemplateArrayException::class));
    }
}
