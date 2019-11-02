<?php
/**
 * @file src/Cruftman/Ldap/Traits/ProvidesSearchQueryInterface.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Traits;

use Korowai\Lib\Ldap\Adapter\ResultInterface;
//use Korowai\Lib\Ldap\Adapter\SearchQueryInterface;

/**
 * @todo Write documentation.
 */
trait ProvidesSearchQueryInterface
{
//    use HasLdapInterface;

    /**
     * {@inheritdoc}
     */
    public function execute() : ResultInterface
    {
        return $this->getSearchQuery()->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function getResult() : ResultInterface
    {
        return $this->getSearchQuery()->getResult();
    }
}

// vim: syntax=php sw=4 ts=4 et:
