<?php
/**
 * @file src/Cruftman/Models/NsUserHelpers.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Models;

trait NsUserHelpers
{
    /**
     * Model namespace, may be overriden in a subclass.
     *
     * @var string
     */
    protected $modelNamespace = __NAMESPACE__;

    /**
     * Returns the namespace where we expect our model to live.
     *
     * @return string
     */
    public function getModelNamespace() : string
    {
        if (!is_string($this->modelNamespace)) {
            throw new \UnexpectedValueException(static::class . '::$modelNamespace is not a string');
        }
        return rtrim($this->modelNamespace, '\\');
    }
}

// vim: syntax=php sw=4 ts=4 et:
