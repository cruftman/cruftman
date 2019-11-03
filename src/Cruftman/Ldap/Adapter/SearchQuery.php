<?php
/**
 * @file src/Cruftman/Ldap/Adapter/SearchQuery.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Adapter;

use Korowai\Lib\Ldap\Adapter\SearchQueryInterface;
use Korowai\Lib\Ldap\Adapter\ResultInterface;

/**
 * @todo Write documentation
 */
class SearchQuery implements SearchQueryInterface
{
    /**
     * @var SearchQueryInterface[]
     */
    protected $searchQueries = [];

    /**
     * {@inheritdoc}
     */
    public function execute() : ResultInterface
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getResult() : ResultInterface
    {
    }
}

// vim: syntax=php sw=4 ts=4 et:
