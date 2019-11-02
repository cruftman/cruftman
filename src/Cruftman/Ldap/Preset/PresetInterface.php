<?php
/**
 * @file src/Cruftman/Ldap/Preset/AbstractPreset.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Preset;

use Cruftman\Support\TemplateOptionsInterface;
use Cruftman\Ldap\Service;

/**
 * Interface provided by an Ldap Preset.
 */
interface PresetInterface extends TemplateOptionsInterface
{
    /**
     * Returns the related LDAP Service object.
     *
     * @return Service|null
     */
    public function getLdapService() : ?Service;
}

// vim: syntax=php sw=4 ts=4 et:
