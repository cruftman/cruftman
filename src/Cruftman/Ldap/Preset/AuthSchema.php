<?php
/**
 * @file src/Cruftman/Ldap/Preset/AuthSchema.php
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
use Cruftman\Support\PresetAbstract;
use Cruftman\Support\Traits\ValidatesOptions;
//use Cruftman\Ldap\Service;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @todo Write documentation
 */
class AuthSchema extends PresetAbstract
{
    use ValidatesOptions;

    /**
     * {@inheritdoc}
     */
    protected function configureOptionsResolver(OptionsResolver $resolver)
    {
        $resolver->setRequired(['sources'])
                 ->setDefined(['ambiguous', 'arguments'])
                 ->setAllowedTypes('sources', 'array')
                 ->setAllowedTypes('ambiguous', 'string')
                 ->setDefault('arguments', function (OptionsResolver $nested) {
                     $nested->setDefined(['useruuid', 'username', 'password'])
                            ->setAllowedTypes('useruuid', 'string')
                            ->setAllowedTypes('username', 'string')
                            ->setAllowedTypes('password', 'string');
                 })
                 ->setAllowedValues('ambiguous', ['first', 'each', 'fail']);
    }

    /**
     * @todo Write documentation.
     * @return Source[]
     */
    public function getSources() : array
    {
        return $this->getRelatedPresetsArrayOrFail(AuthSource::class, 'sources');
    }

    /**
     * @todo Write documentation
     * @return string
     */
    public function getAmbiguous() : string
    {
        return $this->getOption('ambiguous', 'fail');
    }
}

// vim: syntax=php sw=4 ts=4 et:
