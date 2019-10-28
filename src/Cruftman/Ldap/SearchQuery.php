<?php
/**
 * @file src/Cruftman/Ldap/SearchQuery.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap;

use Korowai\Lib\Ldap\LdapInterface;
use Korowai\Lib\Ldap\Adapter\SearchQueryInterface;
use Korowai\Lib\Ldap\Adapter\ResultInterface;

use Cruftman\Ldap\Traits\HasLdapInstance;

/**
 * @todo Write documentation
 */
class SearchQuery implements SearchQueryInterface
{
    use HasLdapInstance;

    /**
     * @var \Korowai\Lib\Ldap\Adapter\SearchQueryInterface
     */
    protected $query;

    /**
     * Initializes the object.
     *
     * @param  \Korowai\Lib\Ldap\Adapter\SearchQueryInterface $query
     * @param  \Korowai\Lib\Ldap\LdapInterface $ldap
     */
    public function __construct(SearchQueryInterface $query, LdapInterface $ldap)
    {
        $this->query = $query;;
        $this->setLdapInstance($ldap);
    }

     /**
     * Executes query and returns result.
     *
     * @return ResultInterface
     */
    public function execute() : ResultInterface
    {
        return $this->query->execute();
    }

    /**
     * Returns the result of last execution of the query, calls execute() if
     * necessary.
     *
     * @return ResultInterface
     */
    public function getResult() : ResultInterface
    {
        return $this->query->getResult();
    }
}

// vim: syntax=php sw=4 ts=4 et:
