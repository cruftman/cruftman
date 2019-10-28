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
     * @return array
     */
    public function getOptions() : TemplateArray
    {
        return $this->templateOptions;
    }

    public function substOptions(array $dict = [])
    {
        return $this->getOptions()->substitute($dict);
    }

    public function substOption(string $key, array $dict = [], $default = null)
    {
        return $this->getOptions()->substitute($dict, $key, $default);
    }
}

// vim: syntax=php sw=4 ts=4 et:
