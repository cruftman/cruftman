<?php
/**
 * @file tests/Helpers/Ldap/ResultInterfaceMockingHelper.php
 *
 * This file is part of the Cruftman package
 *
 * @author Paweł Tomulik <ptomulik@meil.pw.edu.pl>
 * @package cruftman\framework
 * @license Distributed under MIT license.
 */

declare(strict_types=1);

namespace Tests\Helpers\Ldap;

use Korowai\Lib\Ldap\Adapter\ResultInterface;
use Korowai\Lib\Ldap\Entry;

use Tests\Helpers\MockingHelper;

/**
 * Methods that facilitate mocking LdapInterface.
 */
trait ResultInterfaceMockingHelper
{
    use MockingHelper;

    protected function getResultInterfaceMockMethods()
    {
        return ['getResultEntryIterator', 'getResultReferenceIterator', 'getEntries'];
    }

    protected function configureResultInterfaceMock(ResultInterface $mock, array $config)
    {
        $methods = $this->getResultInterfaceMockMethods();
        $this->configureMock($mock, $methods, $config);

//        if (array_key_exists('entries', $config)) {
//            $this->configureResultInterfaceMockEntries($mock, $config['entries']);
//        }
    }

//    protected function configureResultInterfaceMockEntries(ResultInterface $mock, array $entries)
//    {
//        $callback = function (bool $use_keys) use ($entries) {
//            $array = [];
//            foreach ($entries as $dn => $attribs) {
//                if ($use_keys) {
//                    /* $array[$dn] = new ResultEntryInterface($dn, $attribs) ; */
//                } else {
//                    /* $array[] = new ResultEntryInterface($dn, $attribs); */
//                }
//            }
//            return $array;
//        };
//
//        $mock->expects($this->any())
//             ->method('getEntries')
//             ->will($this->returnCallback($callback));
//    }
}

// vim: syntax=php sw=4 ts=4 et:
