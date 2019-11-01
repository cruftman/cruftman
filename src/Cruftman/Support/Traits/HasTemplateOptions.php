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
use Cruftman\Support\Exceptions\OptionNotFoundException;

/**
 * Similar to <a href="HasOptions.html">HasOptions</a> trait, but wraps
 * options with \Cruftman\Support\TemplateArray object.
 */
trait HasTemplateOptions
{
    use HasOptions;

    /**
     * Wraps $options with TemplateArray object.
     *
     * @param  \Cruftman\Support\TemplateArray $options
     * @return $this
     */
    protected function wrapOptions(array $options)
    {
        return new TemplateArray($options);
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

    /**
     * Get an option using "dot" notation with all placeholders substituted.
     *
     * @param  string $key
     * @param  array $dict
     * @return mixed
     * @throws \Cruftman\Support\Exceptions\UndefinedOptionException
     */
    public function substOptionOrFail(string $key, array $dict = [])
    {
        $notfound = new class() {
        };
        $option = $this->substOption($key, $dict, $notfound);
        if ($option === $notfound) {
            throw new OptionNotFoundException('option "'.$key.'" not found');
        }
        return $option;
    }
}

// vim: syntax=php sw=4 ts=4 et:
