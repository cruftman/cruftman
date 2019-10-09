<?php
/**
 * @file src/Cruftman/Api/Transformer/Factory.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Api\Transformer;

use Dingo\Api\Transformer\Factory as DingoTransformerFactory;

class Factory extends DingoTransformerFactory
{
    /**
     * Only to fix the problem that Dingo\Api\Transformer\Factory::getBinding() is not public.
     */
    public function getTransformerBinding($class)
    {
        return $this->getBinding($class);
    }
}

// vim: syntax=php sw=4 ts=4 et:
