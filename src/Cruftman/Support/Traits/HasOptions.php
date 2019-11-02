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
 * Adds protected attribute named ``$options`` and few function to access it.
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
     * Sets ``$options`` to ``$this->options``.
     *
     * If a method named ``validateOptions()`` exists, then the following
     * transformation is performed:
     *
     *      $options = $this->valdateOptions($options);
     *
     * Similarly, if a method named ``wrapOptions()`` exists, then it gets
     * called as follows:
     *
     *      $options = $this->wrapOptions($options);
     *
     * The final result of these two transformations (in the order mentioned)
     * is assigned to ``$this->options``.
     *
     * @param  array $options
     * @return object $this
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
     * Returns ``$this->options``.
     *
     * @return mixed
     */
    public function getOptions()
    {
        return $this->options;
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
            throw new OptionNotFoundException('option "'.$key.'" not found');
        }
        return $option;
    }
}

// vim: syntax=php sw=4 ts=4 et:
