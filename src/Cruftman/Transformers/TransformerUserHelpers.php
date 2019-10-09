<?php
/**
 * @file src/Cruftman/Transformers/TransformerUserHelpers.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Transformers;

use League\Fractal\TransformerAbstract;

/**
 * The receiver should either define ``$transformerName`` property or provide
 * ``getModelName()`` method.
 */
trait TransformerUserHelpers
{
    use NsUserHelpers;

    /**
     * Returns the base name of the associated transformer class.
     *
     * @return string
     */
    public function getTransformerName() : string
    {
        if (isset($this->transformerName)) {
            if (!is_string($this->transformerName)) {
                throw new \UnexpectedValueException(static::class . '::$transformerName is not a string');
            }
            return $this->transformerName;
        } else {
            return $this->getModelName() . 'Transformer';
        }
    }

    /**
     * Returns the transformer class for the controller.
     *
     * @return string
     */
    public function getTransformerClass()
    {
        if (isset($this->transformerClass)) {
            return $this->transformerClass;
        } else {
            return $this->getTransformerNamespace() . '\\' . $this->getTransformerName();
        }
    }

    /**
     * Returns a default instance of transformer used by this controller.
     *
     * @return Transformer
     */
    public function getTransformer() : TransformerAbstract
    {
        $class = $this->getTransformerClass();
        return new $class;
    }
}

// vim: syntax=php sw=4 ts=4 et:
