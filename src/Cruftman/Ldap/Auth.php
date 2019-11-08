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
use Cruftman\Ldap\Preset\AuthSchema;
use Cruftman\Ldap\Preset\AuthSource;
use Cruftman\Ldap\Preset\Binding;
use Cruftman\Ldap\Preset\Connection;
use Cruftman\Ldap\Preset\Search;
use Cruftman\Ldap\Preset\Session;
use Cruftman\Ldap\Auth\SearchEntry;
use Korowai\Lib\Ldap\Exception\LdapException;

/**
 * @todo Write documentation
 */
class Auth
{
    use HasLdapService;

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
     * @return AuthSchema
     */
    public function getAuthSchema()
    {
        return $this->getLdapService()->getAuthSchema();
    }

    /**
     * @todo Write documentation.
     * @return AuthSources[]
     */
    public function getAuthSources() : array
    {
        return $this->getAuthSchema()->getSources();
    }

    /**
     * @todo Write documentation.
     * @return string
     */
    public function getAmbiguous() : string
    {
        return $this->getAuthSchema()->getAmbiguous();
    }

    /**
     * @todo Write documentation.
     *
     * @param  array $credentials
     */
    public function attempt(array $credentials = [])
    {
        return $this->attemptDirectBind($credentials);
    }

    /**
     * @todo Write documentation.
     */
    public function attemptDirectBind(array $arguments = [])
    {
        return $this->attemptDirectBindInSources($this->getAuthSources(), $arguments);
    }

    /**
     * @todo Write documentation.
     */
    protected function attemptBindInSources(array $authSources, array $arguments = [])
    {
        foreach ($authSources as $source) {
            if ($source->getSearch() === null) {
                $result = $this->attemptBindInSource($source, $arguments);
                if ($result !== null) {
                    return $result;
                }
            }
        }
        return null;
    }

    /**
     * @todo Write documentation.
     */
    protected function attemptBindInSource(AuthSource $source, array $arguments = [])
    {
        $binding = $source->getAttemptBinding();
        foreach ($source->getAttemptConnections() as $connection) {
            try {
                $ldap = $connection->createLdap($arguments);
                if ($binding->bindLdapInterface($ldap, $arguments)) {
                    return [
                        'ldap' => $ldap,
                        'dn' => $binding->substOption('0', $arguments),
                        'source' => $source,
                        //'binding' => $binding,
                        //'arguments' => $arguments
                    ];
                }
            } catch (LdapException $exception) {
                switch ($exception->getCode()) {
                    case -1:            // Connection problems etc. (try next connection)
                        break;
                    case 0x31:          // Invalid credentials
                        return null;
                    default:
                        throw $exception;
                }
            }
        }
        return null;
    }

    /**
     * @todo Write documentation.
     *
     * @param  array $arguments
     * @return array
     */
    public function search(array $arguments = [])
    {
        return $this->searchInSources($this->getAuthSources(), $arguments);
    }

    /**
     * @todo Write documentation.
     *
     * @param  AuthSource[] $authSources
     * @param  array $arguments
     * @return array
     */
    protected function searchInSources(array $authSources, array $arguments = [])
    {
        $results = [];
        foreach ($authSources as $source) {
            $result = $this->searchInSource($source, $arguments);
            $results = array_merge($results, $result);
        }
        return $results;
    }

    /**
     * @todo Write documentation.
     *
     * @param  AuthSource $source
     * @param  array $arguments
     */
    protected function searchInSource(AuthSource $source, array $arguments = []) : array
    {
        $results = [];
        if (($search = $source->getSearch()) !== null) {
            foreach ($source->getSessions() as $session) {
                if (($result = $this->searchWithSession($search, $session, $arguments)) !== null) {
                    $results = array_merge($results, $result);
                    break; // no need to try remaining sessions
                }
            }
        }
        return array_map(function ($result) use ($source) {
            return $result->setAuthSource($source);
        }, $results);
    }

    /**
     * @todo Write documentation.
     *
     * @param  Search $search
     * @param  Session $session
     * @param  array $arguments
     * @return array|null
     */
    protected function searchWithSession(Search $search, Session $session, array $arguments = []) : ?array
    {
        try {
            $entries = $this->doSearch($search, $session, $arguments);
        } catch (LdapException $exception) {
            return $this->handleSearchException($exceptin);
        }

        $connection = $session->getConnection();
        return array_map(function ($entry) use ($connection) {
            return new SearchEntry($entry, $connection);
        }, $entries);
    }

    /**
     * @todo Write documentation.
     *
     * @param  Search $search
     * @param  Session $session
     * @param  array $arguments
     * @return array
     */
    protected function doSearch(Search $search, Session $session, array $arguments = []) : array
    {
        $ldap = $session->createLdap($arguments);
        $query = $search->createQuery($ldap, $arguments);
        $result = $query->getResult();
        return $result->getEntries(false);
    }

    /**
     * @todo Write documentation.
     *
     * @param  LdapException $exception
     */
    protected function handleSearchException(LdapException $exception)
    {
        switch ($exception->getCode()) {
            case -1:
                return null;
            default:
                throw $exception;
        }
    }
}

// vim: syntax=php sw=4 ts=4 et:
