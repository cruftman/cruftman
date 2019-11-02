<?php
/**
 * @file src/Cruftman/Support/OptionsSubstInterface.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Support;

/**
 * @todo Write documentation.
 */
interface OptionsSubstInterface
{
    /**
     * Returns the options array with all placeholders substituted.
     *
     * @param  array $dict
     * @return array
     */
    public function substOptions(array $dict = []);

    /**
     * Get an option using "dot" notation with all placeholders substituted.
     *
     * @param  string $key
     * @param  array $dict
     * @param  mixed $default
     * @return mixed
     */
    public function substOption(string $key, array $dict = [], $default = null);

    /**
     * Get an option using "dot" notation with all placeholders substituted or
     * throw an exception if the option is not defined.
     *
     * @param  string $key
     * @param  array $dict
     * @return mixed
     * @throws \Cruftman\Support\Exceptions\UndefinedOptionException
     */
    public function substOptionOrFail(string $key, array $dict = []);
}

// vim: syntax=php sw=4 ts=4 et:
