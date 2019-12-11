<?php
/**
 * @file src/Cruftman/Ldap/Tools/Finder.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Tools;

use Cruftman\Ldap\Presets\Search;
use Korowai\Lib\Ldap\Adapter\AdapterInterface;
use Korowai\Lib\Ldap\Adapter\SearchQueryInterface;
use Korowai\Lib\Ldap\Adapter\ResultInterface;

/**
 * Creates instances of LdapInterface.
 */
class Finder
{
    /**
     * Creates an Ldap search query according to Search preset.
     *
     * @param  Search $search
     * @param  AdapterInterface $ldap
     * @param  array $arguments
     * @return SearchQueryInterface
     */
    public function createQuery(Search $search, AdapterInterface $ldap, array $arguments) : SearchQueryInterface
    {
        $base = $search->base($arguments);
        $filter= $search->filter($arguments);
        $options = $search->options($arguments);
        return $ldap->createSearchQuery($base, $filter, $options);
    }

    /**
     * Creates and executes Ldap search query according to Search preset.
     *
     * @param  Search $search
     * @param  AdapterInterface $ldap
     * @param  array $arguments
     * @return ResultInterface
     */
    public function search(Search $search, AdapterInterface $ldap, array $arguments) : ResultInterface
    {
        return $this->createQuery($search, $ldap, $arguments)->getResult();
    }
}

// vim: syntax=php sw=4 ts=4 et:
