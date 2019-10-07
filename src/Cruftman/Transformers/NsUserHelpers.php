<?php
/**
 * @file src/Cruftman/Transformers/NsUserHelpers.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Transformers;

trait NsUserHelpers
{
    /**
     * Transformer namespace, may be overriden in a subclass.
     *
     * @var string
     */
    protected $transformerNamespace = __NAMESPACE__;

    /**
     * Returns the namespace where we expect our transformer to live.
     *
     * @return string
     */
    public function getTransformerNamespace() : string
    {
        if (!is_string($this->transformerNamespace)) {
            throw new \UnexpectedValueException(static::class . '::$transformerNamespace is not a string');
        }
        return rtrim($this->transformerNamespace, '\\');
    }
}

// vim: syntax=php sw=4 ts=4 et:
