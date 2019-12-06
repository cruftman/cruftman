<?php
/**
 * @file src/Cruftman/Ldap/Presets/Session.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Presets;

use Cruftman\Support\Preset;
use Cruftman\Support\Traits\ValidatesOptions;
use Cruftman\Support\Traits\RelatedPreset;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Session preset.
 */
class Session extends Preset
{
    use ValidatesOptions,
        RelatedPreset;

    protected function configureOptionsResolver(OptionsResolver $resolver)
    {
        $resolver->setRequired(['connection'])
                 ->setDefined(['binding'])
                 ->setAllowedTypes('connection', ['string', 'array'])
                 ->setAllowedTypes('binding', ['string', 'array']);
    }

    /**
     * Returns the Connection preset specified in ``'connection'`` option of this preset.
     * @return Connection
     */
    public function connection() : Connection
    {
        return $this->getRelatedPresetOrFail(Connection::class, 'connection');
    }

    /**
     * Returns the Binding preset specified in ``'binding'`` option of this preset.
     * @return Binding|null
     */
    public function binding() : ?Binding
    {
        return $this->getRelatedPreset(Binding::class, 'binding');
    }
}

// vim: syntax=php sw=4 ts=4 et:
