<?php
/**
 * @file src/Cruftman/Transformers/ModelTransformer.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Transformers;

/**
 * Base class for Cruftman Model transformers .
 */
class ModelTransformer extends Transformer
{
    public function transform($model)
    {
        return $model->attributesToArray();
    }
}

// vim: syntax=php sw=4 ts=4 et:
