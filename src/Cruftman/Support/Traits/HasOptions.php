<?php
/**
 * @file src/Cruftman/Support/Traits/HasOptions.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Support\Traits;

use Illuminate\Support\Arr;

use Cruftman\Support\Exceptions\OptionNotFoundException;

/**
 * Adds protected attributes *$options*, *$optionsPrefix* and few function to
 * access them.
 *
 * Also works with <a href="ValidatesOptions.html">ValidatesOptions</a> trait.
 */
trait HasOptions
{
    /**
     * The options.
     *
     * @var mixed
     */
    protected $options = null;

    /**
     * The prefix for option keys. With *$optionsPrefix* set, we assume that our
     * *$options* resemble a nested part of larger configuration array. The
     * *$optionsPrefix* is a key to this sub-array. For example, we can have
     * ``$options = ['opt1' => 'OPT2', 'opt2' => 'OPT2']`` extracted from the
     * configuration array
     * ``['foo' => ['bar' => ['opt1' => 'OPT1', 'opt2' => 'OPT2']]]``
     * in which case the *$optionsPrefix* shall be set to ``'foo.bar'``.
     *
     * @var string
     */
    protected $optionsPrefix = null;

    /**
     * Applies user-defined transformations to *$options* and assigns them to
     * *$this->options*.
     *
     * If a method named *validateOptions()* exists, then the following
     * transformation is performed:
     *
     *      $options = $this->valdateOptions($options);
     *
     * Similarly, if a method named *wrapOptions()* exists, then it gets
     * called as follows:
     *
     *      $options = $this->wrapOptions($options);
     *
     * The final result of these two transformations (in the order mentioned)
     * is assigned to *$this->options*.
     *
     * @param  array $options
     * @return object *$this*
     */
    public function setOptions(array $options)
    {
        if (method_exists($this, 'validateOptions')) {
            $options = $this->validateOptions($options);
        }
        if (method_exists($this, 'wrapOptions')) {
            $options = $this->wrapOptions($options);
        }
        $this->options = $options;
        return $this;
    }

    /**
     * Returns *$this->options*.
     *
     * @return mixed may return array, object or null
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Assigns *$optionsPrefix* to *$this->optionsPrefix*.
     *
     * @param string|null $optionsPrefix
     * @return object $this
     */
    public function setOptionsPrefix(?string $optionsPrefix)
    {
        $this->optionsPrefix = $optionsPrefix;
        return $this;
    }

    /**
     * Returns *$this->optionsPrefix*.
     *
     * @return string|null
     */
    public function getOptionsPrefix() : ?string
    {
        return $this->optionsPrefix;
    }

    /**
     * Returns *$key* with *$this->prefix* prepended.
     *
     * @param string $key
     * @return string
     */
    public function getPrefixedOptionKey(string $key) : string
    {
        $prefix = $this->getOptionsPrefix() ?? '';
        return $prefix ? $prefix . '.' . $key : $key;
    }

    /**
     * Get an option using "dot" notation.
     *
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    public function getOption(string $key, $default = null)
    {
        return Arr::get($this->getOptions(), $key, $default);
    }

    /**
     * Get an option using "dot" notation.
     *
     * @param  string $key
     * @return mixed
     * @throws OptionNotFoundException
     */
    public function getOptionOrFail(string $key)
    {
        $notfound = new class {
        };
        $option = $this->getOption($key, $notfound);
        if ($option === $notfound) {
            throw new OptionNotFoundException('option "'.$this->getPrefixedOptionKey($key).'" not found');
        }
        return $option;
    }
}

// vim: syntax=php sw=4 ts=4 et:
