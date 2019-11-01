<?php
/**
 * @file src/Cruftman/Support/OptionsInterface.php
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
 * Provides access to options array encapsulated by object.
 */
interface OptionsInterface
{
    /**
     * Sets ``$options`` to the object.
     *
     * @param  array $options
     * @return object $this
     */
    public function setOptions(array $options);

    /**
     * Returns the whole array of options set by setOptions().
     *
     * @return mixed
     */
    public function getOptions();

    /**
     * Get a single option value using "dot" notation.
     *
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    public function getOption(string $key, $default = null);

    /**
     * Get an option using "dot" notation if exists or throw an exception
     * otherwise.
     *
     * @param  string $key
     * @return mixed
     * @throws \Cruftman\Support\Exceptions\UndefinedOptionException
     */
    public function getOptionOrFail(string $key);
}

// vim: syntax=php sw=4 ts=4 et:
