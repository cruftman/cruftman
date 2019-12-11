<?php
/**
 * @file src/Cruftman/Ldap/Traits/HasConnectorTool.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Cruftman\Ldap\Traits;

use Cruftman\Ldap\Tools\Connector;

/**
 * Add a protected attribute named *$connector* and public accessors.
 */
trait HasConnectorTool
{
    /**
     * @var Connector
     */
    protected $connector;

    /**
     * Sets Connector tool to the object.
     *
     * @param  Connector $connector
     * @return object $this
     */
    public function setConnector(?Connector $connector)
    {
        $this->connector = $connector;
        return $this;
    }

    /**
     * Returns the Connector tool.
     *
     * @return Connector
     */
    public function getConnector() : Connector
    {
        if ($this->connector === null) {
            $this->setConnector(new Connector);
        }
        return $this->connector;
    }
}

// vim: syntax=php sw=4 ts=4 et:
