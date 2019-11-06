<?php
/**
 * @file src/Cruftman/Ldap/Preset/Auth.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Preset;

//use Korowai\Lib\Ldap\Adapter\AdapterInterface;
//use Korowai\Lib\Ldap\Adapter\SearchQueryInterface;
use Korowai\Lib\Ldap\Exception\LdapException;
use Cruftman\Support\Traits\ValidatesOptions;
//use Cruftman\Ldap\Service;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @todo Write documentation
 */
class Auth extends AbstractPreset
{
    use ValidatesOptions;

    /**
     * {@inheritdoc}
     */
    protected function configureOptionsResolver(OptionsResolver $resolver)
    {
        $resolver->setRequired(['sources'])
                 ->setDefined(['ambiguous'])
                 ->setAllowedTypes('sources', 'array')
                 ->setAllowedTypes('ambiguous', 'string')
                 ->setAllowedValues('ambiguous', ['first', 'each', 'fail']);
    }

    /**
     * @todo Write documentation.
     * @return Source[]
     */
    public function getSources() : array
    {
        $service = $this->getLdapService();
        return array_map(function ($sourceOptions) use ($service) {
            return $service->getAuthSource($sourceOptions);
        }, $this->getOptionOrFail('sources'));
    }

    protected function searchInSource(AuthSource $source, array $arguments = [])
    {
        $results = [];
        if (($search = $source->getSearch()) == null) {
            return $results;
        }

        foreach ($source->getSessions() as $session) {
            try {
                $ldap = $session->createLdap($arguments);
                $query = $search->createQuery($ldap, $arguments);
                $entries = $query->execute()->getEntries(false);
            } catch (LdapException $exception) {
                switch ($exception->getCode()) {
                    case -1:    // connection error and such things...
                        $entries = null;
                        break;
                    default:
                        throw $exception;
                }
            }
            if ($entries !== null) {
                foreach ($entries as $entry) {
                    // FIXME: this is really ugly...
                    $results[] = [
                        'dn' => $entry->getDn(),
                        'attributes' => $entry->getAttributes(),
                        'connection' => $session->getConnection(),
                        'source' => $source
                    ];
                }
                break;
            }
        }
        return $results;
    }

    public function searchInSources(array $sources, array $arguments = [])
    {
        $results = [];
        foreach ($sources as $source) {
            $found = $this->searchInSource($source, $arguments);
            $results = array_merge($results, $found);
        }
        return $results;
    }

    /**
     * @todo Write documentation
     *
     * @param  array $arguments
     */
    public function attempt(array $arguments = [])
    {
        $sources = $this->getSources();

        foreach ($sources as $source) {
        }
    }
}

// vim: syntax=php sw=4 ts=4 et:
