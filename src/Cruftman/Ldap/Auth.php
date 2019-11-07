<?php
/**
 * @file src/Cruftman/Ldap/Service.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap;

use Cruftman\Ldap\Traits\HasLdapService;
use Cruftman\Ldap\Preset\AuthSource;
use Cruftman\Ldap\Preset\Search;
use Cruftman\Ldap\Preset\Session;
use Korowai\Lib\Ldap\Exception\LdapException;

/**
 * @todo Write documentation
 */
class Auth
{
    use HasLdapService;

    /**
     * @var Service
     */
    protected $service;

    /**
     * Initializes the Auth object.
     *
     * @param  Service $service
     */
    public function __construct(Service $service)
    {
        $this->setLdapService($service);
    }

    /**
     * @todo Write documentation.
     *
     * @param  array $credentials
     */
    public function attempt(array $credentials = [])
    {
        $searchResults = $this->search($credentials);
    }

    /**
     * @todo Write documentation.
     *
     * @param  array $arguments
     * @return array
     */
    public function search(array $arguments = [])
    {
        $authSchema = $this->getLdapService()->getAuthSchema();
        $authSources = $authSchema->getSources();
        $results = [];
        foreach ($authSources as $source) {
            $results = array_merge($results, $this->searchSource($source, $arguments));
        }
        return $results;
    }

    /**
     * @todo Write documentation.
     *
     * @param  AuthSource $source
     * @param  array $arguments
     */
    protected function searchSource(AuthSource $source, array $arguments = [])
    {
        $results = [];
        if (($search = $source->getSearch()) !== null) {
            foreach ($source->getSessions() as $session) {
                $result = $this->searchWithSession($search, $session, $arguments);
                if ($result !== null) {
                    $results = array_merge($results, $result);
                    break;
                }
            }
        }
        return $results;
    }

    /**
     * @todo Write documentation.
     *
     * @param  AuthSource $source
     * @param  Session $session
     * @param  array $arguments
     */
    protected function searchWithSession(Search $search, Session $session, array $arguments = [])
    {
        try {
            $ldap = $session->createLdap($arguments);
            $query = $search->createQuery($ldap, $arguments);
            $result = $query->getResult();
            $entries = $result->getEntries(false);
        } catch (LdapException $exception) {
            switch ($exception->getCode()) {
                case -1:
                    return null;
                default:
                    throw $exception;
            }
        }

        return array_map(function ($entry) use ($session) {
            // FIXME: this is ugly...
            return [
                'dn' => $entry->getDn(),
                'attributes' => $entry->getAttributes(),
                'connection' => $session->getConnection(),
            ];
        }, $entries);

    }
}

// vim: syntax=php sw=4 ts=4 et:
