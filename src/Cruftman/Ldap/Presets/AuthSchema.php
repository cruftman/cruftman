<?php
/**
 * @file src/Cruftman/Ldap/Presets/AuthSchema.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Presets;

//use Korowai\Lib\Ldap\Adapter\AdapterInterface;
//use Korowai\Lib\Ldap\Adapter\SearchQueryInterface;
use Korowai\Lib\Ldap\Exception\LdapException;
use Cruftman\Support\Preset;
use Cruftman\Support\Traits\ValidatesOptions;
use Cruftman\Support\Traits\RelatedPresetsArray;
//use Cruftman\Ldap\Service;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @todo Write documentation
 */
class AuthSchema extends Preset
{
    use ValidatesOptions,
        RelatedPresetsArray;

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
     * Returns an array of AuthSource presets as defined in ``'sources'`` option.
     * @return AuthSource[]
     */
    public function sources() : array
    {
        return $this->getRelatedPresetsArrayOrFail(AuthSource::class, 'sources');
    }

    /**
     * Returns the value of ``'ambiguous'`` option of this preset.
     *
     * @param mixed $default default value to be used when ambiguous option is not set.
     * @return string|null
     */
    public function ambiguous(?string $default = null) : ?string
    {
        return $this->getOption('ambiguous', $default);
    }

    /**
     * Returns the ``'arguments'`` array of arguments' mappings of this preset.
     *
     * @param mixed $default default mapping to be used when ``arguments`` option was not set.
     * @return string|null
     */
    public function arguments(?array $default = null) : ?array
    {
        return $this->getOption('arguments', $default);
    }
}

// vim: syntax=php sw=4 ts=4 et:
