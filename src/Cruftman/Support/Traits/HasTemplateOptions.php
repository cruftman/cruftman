<?php
/**
 * @file src/Cruftman/Support/Traits/HasTemplateOptions.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Support\Traits;

use Cruftman\Support\TemplateArray;

/**
 * @todo Write documentation.
 */
trait HasTemplateOptions
{
    /**
     * @var \Cruftman\Support\TemplateArray
     */
    protected $templateOptions = null;

    /**
     * Sets $templateOptions to the object.
     *
     * @param  \Cruftman\Support\TemplateArray $templateOptions
     * @return $this
     */
    public function setOptions(array $options)
    {
        if (method_exists($this, 'validateOptions')) {
            $options = $this->validateOptions($options);
        }
        $this->templateOptions = new TemplateArray($options);
        return $this;
    }

    /**
     * Returns the $templateOptions.
     *
     * @return \Cruftman\Support\TemplateArray
     */
    public function getOptions() : TemplateArray
    {
        return $this->templateOptions;
    }

    /**
     * Get a single option (without substituting placeholders) using "dot" notation.
     *
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    public function getOption(string $key, $default = null)
    {
        return $this->getOptions()->get($key, $default);
    }

    /**
     * Returns the options array with all placeholders substituted.
     *
     * @param  array $dict
     * @return array
     */
    public function substOptions(array $dict = [])
    {
        return $this->getOptions()->substitute($dict);
    }

    /**
     * Get an option using "dot" notation with all placeholders substituted.
     *
     * @param  string $key
     * @param  array $dict
     * @param  mixed $default
     * @return mixed
     */
    public function substOption(string $key, array $dict = [], $default = null)
    {
        return $this->getOptions()->substItem($key, $dict, $default);
    }
}

// vim: syntax=php sw=4 ts=4 et:
