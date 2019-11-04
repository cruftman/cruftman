<?php
/**
 * @file src/Cruftman/Ldap/Preset/Search.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Preset;

use Korowai\Lib\Ldap\Adapter\SearchQueryInterface;
use Cruftman\Support\Traits\ValidatesOptions;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Search preset.
 */
class Search extends AbstractPreset
{
    use ValidatesOptions;

    protected function configureOptionsResolver(OptionsResolver $resolver)
    {
        $resolver->setRequired(['session', 'base', 'filter'])
                 ->setDefined(['options'])
                 ->setAllowedTypes('session', ['string', 'array'])
                 ->setAllowedTypes('base', 'string')
                 ->setAllowedTypes('filter', 'string')
                 ->setAllowedTypes('options', 'array');
    }

    /**
     * Creates and returns an instance of SearchQueryInterface.
     *
     * @param  array $arguments
     * @return SearchQueryInterface
     */
    public function createSearchQuery(array $arguments = []) : SearchQueryInterface
    {
        $base = $this->substOptionOrFail('base', $arguments);
        $filter = $this->substOptionOrFail('filter', $arguments);
        $options = $this->substOption('options', $arguments, []);

        $sessionOption = $this->substOptionOrFail('session', $arguments);
        $session = $this->getLdapService()->session($sessionOption);

        $ldap = $session->createLdap($arguments);

        return $ldap->createSearchQuery($base, $filter, $options);
    }
}

// vim: syntax=php sw=4 ts=4 et:
