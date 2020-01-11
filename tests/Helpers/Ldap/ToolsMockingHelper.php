<?php
/**
 * @file tests/Helpers/Ldap/ToolsMockingHelper.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Tests\Helpers\Ldap;

use Cruftman\Ldap\Tools\Binder;
use Cruftman\Ldap\Tools\Finder;
use Cruftman\Ldap\Tools\Connector;
use Tests\Helpers\MockingHelper;

/**
 * Methods that facilitate mocking Tool.
 */
trait ToolsMockingHelper
{
    use MockingHelper;
    use Tools\BinderMockingHelper;
    use Tools\FinderMockingHelper;
    use Tools\ConnectorMockingHelper;
}

// vim: syntax=php sw=4 ts=4 et:
