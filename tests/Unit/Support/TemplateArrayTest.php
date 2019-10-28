<?php

namespace Tests\Unit\Support;

use PHPUnit\Framework\TestCase;
use Cruftman\Support\TemplateArray;
use Cruftman\Support\Exceptions\TemplateArrayException;

class TemplateArrayTest extends TestCase
{
    /**
     * Ensure that class TemplateArray extends \ArrayObject
     *
     * @return void
     */
    public function test__extendsArrayObject()
    {
        $this->assertContains(\ArrayObject::class, class_parents(TemplateArray::class));
    }

    /**
     * Ensure that certain constants are defined.
     *
     * @return void
     */
    public function test__staticConstants()
    {
        $this->assertSame('none', TemplateArray::UNDEF_NONE);
        $this->assertSame('skip', TemplateArray::UNDEF_SKIP);
        $this->assertSame('fail', TemplateArray::UNDEF_FAIL);
    }

    /**
     * Ensure that ``__construct($template)`` works.
     *
     * @return void
     */
    public function test__construct__withTemplate()
    {
        $array = ['foo' => 'FOO', 'bar' => 'BAR'];
        $tarr = new TemplateArray($array);
        $this->assertSame($array, $tarr->getArrayCopy());
        $this->assertSame([], $tarr->getDefaults());
    }

    /**
     * Ensure that ``__construct($template, $defaults)`` works.
     *
     * @return void
     */
    public function test__construct__withTemplateAndDefaults()
    {
        $array = ['foo' => 'FOO', 'bar' => 'BAR'];
        $defaults = ['geez' => 'GEEZ'];
        $tarr = new TemplateArray($array, $defaults);
        $this->assertSame($array, $tarr->getArrayCopy());
        $this->assertSame($defaults, $tarr->getDefaults());
    }

    /**
     * Ensure that ``getUndefAction()`` and ``setUndefAction()`` work.
     *
     * @return void
     */
    public function test__getUndefAction__setUndefAction()
    {
        $tarr = new TemplateArray([]);

        $this->assertSame(TemplateArray::UNDEF_FAIL, $tarr->getUndefAction());

        $this->assertSame($tarr, $tarr->setUndefAction(TemplateArray::UNDEF_SKIP));
        $this->assertSame(TemplateArray::UNDEF_SKIP, $tarr->getUndefAction());

        $this->assertSame($tarr, $tarr->setUndefAction(TemplateArray::UNDEF_NONE));
        $this->assertSame(TemplateArray::UNDEF_NONE, $tarr->getUndefAction());
    }

    /**
     * Ensure that ``getPattern()`` and ``setPattern()`` work.
     *
     * @return void
     */
    public function test__getPattern__setPattern()
    {
        $tarr = new TemplateArray([]);

        $this->assertSame('/\${([[:alpha:]_][[:alnum:]_]*)}/u', $tarr->getPattern());

        $pattern = '/%([[:alpha:]_][[:alnum:]_]*)%/u';
        $this->assertSame($tarr, $tarr->setPattern($pattern));
        $this->assertSame($pattern, $tarr->getPattern());
    }

    /**
     * Ensure that ``getFormat()`` and ``setFormat()`` work.
     *
     * @return void
     */
    public function test__getFormat__setFormat()
    {
        $tarr = new TemplateArray([]);

        $this->assertSame('${%s}', $tarr->getFormat());

        $format = '%%%s%%';
        $this->assertSame($tarr, $tarr->setFormat($format));
        $this->assertSame($format, $tarr->getFormat());
    }

    /**
     * Ensure that ``isRecursive()`` and ``setRecursive()`` work.
     *
     * @return void
     */
    public function test__isRecursive__setRecursive()
    {
        $tarr = new TemplateArray([]);

        $this->assertTrue($tarr->isRecursive());

        $this->assertSame($tarr, $tarr->setRecursive(false));
        $this->assertFalse($tarr->isRecursive());

        $this->assertSame($tarr, $tarr->setRecursive(true));
        $this->assertTrue($tarr->isRecursive());

        $this->assertSame($tarr, $tarr->setRecursive(false));
        $this->assertFalse($tarr->isRecursive());

        $this->assertSame($tarr, $tarr->setRecursive());
        $this->assertTrue($tarr->isRecursive());
    }

    /**
     * Ensure that ``getDefaults()`` and ``setDefaults()`` work.
     *
     * @return void
     */
    public function test__getDefaults__setDefaults()
    {
        $tarr = new TemplateArray([]);

        $this->assertSame([], $tarr->getDefaults());

        $defaults = ['geez' => 'GEEZ'];
        $this->assertSame($tarr, $tarr->setDefaults($defaults));
        $this->assertSame($defaults, $tarr->getDefaults());
    }

    /**
     * Ensure that ``placeholder()`` works.
     *
     * @return void
     */
    public function test__placeholder()
    {
        $tarr = new TemplateArray([]);

        $this->assertSame('${foo}', $tarr->placeholder('foo'));

        $this->assertSame($tarr, $tarr->setFormat('%%{%s}%%'));
        $this->assertSame('%{foo}%', $tarr->placeholder('foo'));
    }

    /**
     * Ensure that ``get()`` works.
     *
     * @return void
     */
    public function test__get()
    {
        $template = ['filter' => '(uid=${username})', 'options' => ['scope' => '${scope}']];

        $tarr = new TemplateArray($template);
        $this->assertSame('(uid=${username})', $tarr->get('filter'));
        $this->assertSame(['scope' => '${scope}'], $tarr->get('options'));
        $this->assertSame('${scope}', $tarr->get('options.scope'));
        $this->assertNull($tarr->get('foo'));
        $this->assertSame('DEFAULT', $tarr->get('foo.bar', 'DEFAULT'));
    }

    /**
     * Ensure that ``substitute()`` works with default settings.
     *
     * @return void
     */
    public function test__substitute()
    {
        $template = ['filter' => '(uid=${username})', 'options' => ['scope' => '${scope}']];
        $expect01 = ['filter' => '(uid=jsmith)', 'options' => ['scope' => 'one']];
        $expect02 = ['filter' => '(uid=mbaker)', 'options' => ['scope' => 'sub']];

        $tarr = new TemplateArray($template);
        $this->assertSame($expect01, $tarr->substitute(['username' => 'jsmith', 'scope' => 'one']));
        $this->assertSame($expect02, $tarr->substitute(['username' => 'mbaker', 'scope' => 'sub']));
    }

    /**
     * Ensure that ``substitute()`` throws TemplateArrayException.
     *
     * @return void
     */
    public function test__substitute__throwsTemplateArrayException01()
    {
        $template = ['filter' => '(uid=${username})', 'options' => ['scope' => '${scope}']];
        $tarr = new TemplateArray($template);

        $this->expectException(TemplateArrayException::class);
        $this->expectExceptionMessage('undefined value for ${username} placeholder');

        $tarr->substitute(['scope' => 'one']);
    }

    /**
     * Ensure that ``substitute()`` throws TemplateArrayException.
     *
     * @return void
     */
    public function test__substitute__throwsTemplateArrayException02()
    {
        $template = ['filter' => '(uid=${username})', 'options' => ['scope' => '${scope}']];
        $tarr = new TemplateArray($template);

        $this->expectException(TemplateArrayException::class);
        $this->expectExceptionMessage('undefined value for ${scope} placeholder');

        $tarr->substitute(['username' => 'jsmith']);
    }

    /**
     * Ensure that ``substitute()`` throws TemplateArrayException.
     *
     * @return void
     */
    public function test__substitute__throwsTemplateArrayException03()
    {
        $template = ['filter' => '(uid=${username})', 'options' => ['scope' => '${scope}']];
        $tarr = new TemplateArray($template);

        $this->expectException(TemplateArrayException::class);
        $this->expectExceptionMessage('undefined value for ${scope} placeholder');

        $tarr->substitute(['username' => 'jsmith']);
    }

    /**
     * Ensure that ``substitute()`` removes undefined placeholders.
     *
     * @return void
     */
    public function test__substitute__removesUndefinedPlaceholders()
    {
        $template = ['filter' => '(uid=${username})', 'options' => ['scope' => '${scope}']];
        $expect01 = ['filter' => '(uid=jsmith)', 'options' => ['scope' => '']];
        $expect02 = ['filter' => '(uid=)', 'options' => ['scope' => 'one']];
        $expect03 = ['filter' => '(uid=)', 'options' => ['scope' => '']];

        $tarr = (new TemplateArray($template))->setUndefAction(TemplateArray::UNDEF_NONE);

        $this->assertSame($expect01, $tarr->substitute(['username' => 'jsmith']));
        $this->assertSame($expect02, $tarr->substitute(['scope' => 'one']));
        $this->assertSame($expect03, $tarr->substitute([]));
    }

    /**
     * Ensure that ``substitute()`` skips undefined placeholders.
     *
     * @return void
     */
    public function test__substitute__skipsUndefinedPlaceholders()
    {
        $template = ['filter' => '(uid=${username})', 'options' => ['scope' => '${scope}']];
        $expect01 = ['filter' => '(uid=jsmith)', 'options' => ['scope' => '${scope}']];
        $expect02 = ['filter' => '(uid=${username})', 'options' => ['scope' => 'one']];
        $expect03 = ['filter' => '(uid=${username})', 'options' => ['scope' => '${scope}']];

        $tarr = (new TemplateArray($template))->setUndefAction(TemplateArray::UNDEF_SKIP);

        $this->assertSame($expect01, $tarr->substitute(['username' => 'jsmith']));
        $this->assertSame($expect02, $tarr->substitute(['scope' => 'one']));
        $this->assertSame($expect03, $tarr->substitute([]));
    }

    /**
     * Ensure that ``substitute()`` respects "recursive" flag.
     *
     * @return void
     */
    public function test__substitute__respectsRecursiveFlag()
    {
        $template = ['filter' => '(uid=${username})', 'options' => ['scope' => '${scope}']];
        $expect_0 = ['filter' => '(uid=jsmith)', 'options' => ['scope' => '${scope}']]; // recursive = false
        $expect_1 = ['filter' => '(uid=jsmith)', 'options' => ['scope' => 'one']];      // recursive = true

        $tarr = new TemplateArray($template);

        $this->assertSame($expect_1, $tarr->substitute(['username' => 'jsmith', 'scope' => 'one']));

        $tarr->setRecursive(false);
        $this->assertSame($expect_0, $tarr->substitute(['username' => 'jsmith', 'scope' => 'one']));
        $this->assertSame($expect_0, $tarr->substitute(['username' => 'jsmith']));

        $tarr->setRecursive();
        $this->assertSame($expect_1, $tarr->substitute(['username' => 'jsmith', 'scope' => 'one']));
    }

    /**
     * Ensure that ``substitute()`` respects defaults.
     *
     * @return void
     */
    public function test__substitute__respectsDefaults()
    {
        $template = ['filter' => '(uid=${username})', 'options' => ['scope' => '${scope}']];
        $expect_0 = ['filter' => '(uid=jsmith)', 'options' => ['scope' => 'one']]; // recursive = false
        $expect_1 = ['filter' => '(uid=mbaker)', 'options' => ['scope' => 'sub']];      // recursive = true
        $expect_2 = ['filter' => '(uid=jsmith)', 'options' => ['scope' => 'sub']];      // recursive = true
        $expect_3 = ['filter' => '(uid=mbaker)', 'options' => ['scope' => 'one']];      // recursive = true

        $tarr = new TemplateArray($template, ['username' => 'mbaker', 'scope' => 'sub']);

        $this->assertSame($expect_0, $tarr->substitute(['username' => 'jsmith', 'scope' => 'one']));
        $this->assertSame($expect_1, $tarr->substitute());
        $this->assertSame($expect_2, $tarr->substitute(['username' => 'jsmith']));
        $this->assertSame($expect_3, $tarr->substitute(['scope' => 'one']));
    }

    /*
     * Ensure that ``substitute()`` with ``$key`` parameter works.
     *
     * @return void
     */
    public function test__substitute__withKey()
    {
        $template = ['filter' => '(uid=${username})', 'options' => ['scope' => '${scope}'], 'number' => 123];

        $tarr = new TemplateArray($template);

        $this->assertSame('(uid=jsmith)', $tarr->substitute(['username' => 'jsmith', 'scope' => 'one'], 'filter'));
        $this->assertSame('(uid=jsmith)', $tarr->substitute(['username' => 'jsmith'], 'filter'));

        $this->assertSame(['scope' => 'one'], $tarr->substitute(['username' => 'jsmith', 'scope' => 'one'], 'options'));
        $this->assertSame(['scope' => 'one'], $tarr->substitute(['scope' => 'one'], 'options'));

        $this->assertSame('one', $tarr->substitute(['username' => 'jsmith', 'scope' => 'one'], 'options.scope'));
        $this->assertSame('one', $tarr->substitute(['scope' => 'one'], 'options.scope'));

        $this->assertSame(123, $tarr->substitute([], 'number'));

        $this->assertNull($tarr->substitute([], 'inexistent'));
        $this->assertSame(123, $tarr->substitute([], 'inexistent', 123));
    }

    /*
     * Ensure that ``substitute()`` with ``$key`` parameter throws TemplateArrayException.
     *
     * @return void
     */
    public function test__substitute__withKey__throwsTemplateArrayException01()
    {
        $template = ['filter' => '(uid=${username})', 'options' => ['scope' => '${scope}']];

        $tarr = new TemplateArray($template);

        $this->assertSame(['scope' => 'one'], $tarr->substitute(['scope' => 'one'], 'options'));
        $this->assertSame('one', $tarr->substitute(['scope' => 'one'], 'options.scope'));

        $this->expectException(TemplateArrayException::class);
        $this->expectExceptionMessage('undefined value for ${username} placeholder');

        $tarr->substitute(['scope' => 'one'], 'filter');
    }

    /*
     * Ensure that ``substitute()`` with ``$key`` parameter throws TemplateArrayException.
     *
     * @return void
     */
    public function test__substitute__withKey__throwsTemplateArrayException02()
    {
        $template = ['filter' => '(uid=${username})', 'options' => ['scope' => '${scope}']];

        $tarr = new TemplateArray($template);

        $this->assertSame('(uid=jsmith)', $tarr->substitute(['username' => 'jsmith'], 'filter'));

        $this->expectException(TemplateArrayException::class);
        $this->expectExceptionMessage('undefined value for ${scope} placeholder');

        $tarr->substitute(['username' => 'jsmith'], 'options');
    }

    /*
     * Ensure that ``substitute()`` with ``$key`` parameter throws TemplateArrayException.
     *
     * @return void
     */
    public function test__substitute__withKey__throwsTemplateArrayException03()
    {
        $template = ['filter' => '(uid=${username})', 'options' => ['scope' => '${scope}']];

        $tarr = new TemplateArray($template);

        $this->expectException(TemplateArrayException::class);
        $this->expectExceptionMessage('undefined value for ${scope} placeholder');

        $tarr->substitute(['username' => 'jsmith'], 'options.scope');
    }

    /**
     * Ensure that ``substitute()`` with ``$key`` parameter respects "recursive" flag.
     *
     * @return void
     */
    public function test__substitute__withKey__respectsRecursiveFlag()
    {
        $template = [
            'filter' => '(uid=${username})',
            'options' => ['scope' => '${scope}'],
            'top' => ['bottom' => ['${value}']]
        ];

        $tarr = new TemplateArray($template);

        $this->assertSame('(uid=jsmith)', $tarr->substitute(['username' => 'jsmith', 'scope' => 'one'], 'filter'));

        $tarr->setRecursive(false);
        $this->assertSame('(uid=jsmith)', $tarr->substitute(['username' => 'jsmith'], 'filter'));
        $this->assertSame(['scope' => 'one'], $tarr->substitute(['scope' => 'one'], 'options'));
        $this->assertSame(['bottom' => ['${value}']], $tarr->substitute(['value' => 'FOO'], 'top'));
        $this->assertSame(['FOO'], $tarr->substitute(['value' => 'FOO'], 'top.bottom'));
        $this->assertSame('FOO', $tarr->substitute(['value' => 'FOO'], 'top.bottom.0'));

        $tarr->setRecursive();
        $this->assertSame('(uid=jsmith)', $tarr->substitute(['username' => 'jsmith'], 'filter'));
        $this->assertSame(['scope' => 'one'], $tarr->substitute(['scope' => 'one'], 'options'));
        $this->assertSame(['bottom' => ['FOO']], $tarr->substitute(['value' => 'FOO'], 'top'));
        $this->assertSame(['FOO'], $tarr->substitute(['value' => 'FOO'], 'top.bottom'));
        $this->assertSame('FOO', $tarr->substitute(['value' => 'FOO'], 'top.bottom.0'));
    }

    /*
     * Ensure that ``substItem()`` works.
     *
     * @return void
     */
    public function test__substItem()
    {
        $template = ['filter' => '(uid=${username})', 'options' => ['scope' => '${scope}'], 'number' => 123];

        $tarr = new TemplateArray($template);

        $this->assertSame('(uid=jsmith)', $tarr->substItem('filter', ['username' => 'jsmith', 'scope' => 'one']));
        $this->assertSame('(uid=jsmith)', $tarr->substItem('filter', ['username' => 'jsmith']));

        $this->assertSame(['scope' => 'one'], $tarr->substItem('options', ['username' => 'jsmith', 'scope' => 'one']));
        $this->assertSame(['scope' => 'one'], $tarr->substItem('options', ['scope' => 'one']));

        $this->assertSame('one', $tarr->substitute(['username' => 'jsmith', 'scope' => 'one'], 'options.scope'));
        $this->assertSame('one', $tarr->substitute(['scope' => 'one'], 'options.scope'));

        $this->assertSame(123, $tarr->substitute([], 'number'));

        $this->assertNull($tarr->substitute([], 'inexistent'));
        $this->assertSame(123, $tarr->substitute([], 'inexistent', 123));
    }

    /*
     * Ensure that ``substItem()`` throws TemplateArrayException.
     *
     * @return void
     */
    public function test__substItem__throwsTemplateArrayException01()
    {
        $template = ['filter' => '(uid=${username})', 'options' => ['scope' => '${scope}']];

        $tarr = new TemplateArray($template);

        $this->assertSame(['scope' => 'one'], $tarr->substItem('options', ['scope' => 'one']));
        $this->assertSame('one', $tarr->substitute(['scope' => 'one'], 'options.scope'));

        $this->expectException(TemplateArrayException::class);
        $this->expectExceptionMessage('undefined value for ${username} placeholder');

        $tarr->substItem('filter', ['scope' => 'one']);
    }

    /*
     * Ensure that ``substItem()`` throws TemplateArrayException.
     *
     * @return void
     */
    public function test__substItem__throwsTemplateArrayException02()
    {
        $template = ['filter' => '(uid=${username})', 'options' => ['scope' => '${scope}']];

        $tarr = new TemplateArray($template);

        $this->assertSame('(uid=jsmith)', $tarr->substItem('filter', ['username' => 'jsmith']));

        $this->expectException(TemplateArrayException::class);
        $this->expectExceptionMessage('undefined value for ${scope} placeholder');

        $tarr->substItem('options', ['username' => 'jsmith']);
    }

    /*
     * Ensure that ``substItem()`` throws TemplateArrayException.
     *
     * @return void
     */
    public function test__substItem__throwsTemplateArrayException03()
    {
        $template = ['filter' => '(uid=${username})', 'options' => ['scope' => '${scope}']];

        $tarr = new TemplateArray($template);

        $this->expectException(TemplateArrayException::class);
        $this->expectExceptionMessage('undefined value for ${scope} placeholder');

        $tarr->substitute(['username' => 'jsmith'], 'options.scope');
    }

    /**
     * Ensure that ``substItem()`` respects "recursive" flag.
     *
     * @return void
     */
    public function test__substItem__respectsRecursiveFlag()
    {
        $template = [
            'filter' => '(uid=${username})',
            'options' => ['scope' => '${scope}'],
            'top' => ['bottom' => ['${value}']]
        ];

        $tarr = new TemplateArray($template);

        $this->assertSame('(uid=jsmith)', $tarr->substItem('filter', ['username' => 'jsmith', 'scope' => 'one']));

        $tarr->setRecursive(false);
        $this->assertSame('(uid=jsmith)', $tarr->substItem('filter', ['username' => 'jsmith']));
        $this->assertSame(['scope' => 'one'], $tarr->substItem('options', ['scope' => 'one']));
        $this->assertSame(['bottom' => ['${value}']], $tarr->substItem('top', ['value' => 'FOO']));
        $this->assertSame(['FOO'], $tarr->substitute(['value' => 'FOO'], 'top.bottom'));
        $this->assertSame('FOO', $tarr->substitute(['value' => 'FOO'], 'top.bottom.0'));

        $tarr->setRecursive();
        $this->assertSame('(uid=jsmith)', $tarr->substItem('filter', ['username' => 'jsmith']));
        $this->assertSame(['scope' => 'one'], $tarr->substItem('options', ['scope' => 'one']));
        $this->assertSame(['bottom' => ['FOO']], $tarr->substItem('top', ['value' => 'FOO']));
        $this->assertSame(['FOO'], $tarr->substitute(['value' => 'FOO'], 'top.bottom'));
        $this->assertSame('FOO', $tarr->substitute(['value' => 'FOO'], 'top.bottom.0'));
    }
}
