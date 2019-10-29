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

/**
 * @todo Write documentation.
 */
trait HasOptions
{
    /**
     * @var \Cruftman\Support\TemplateArray
     */
    protected $options = null;

    /**
     * Sets $options to the object.
     *
     * @param  \Cruftman\Support\TemplateArray $options
     * @return $this
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
     * Returns the $options.
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
}

// vim: syntax=php sw=4 ts=4 et:
