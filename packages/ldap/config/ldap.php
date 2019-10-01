<?php
/**
 * @file config/ldap.php
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Servers
    |--------------------------------------------------------------------------
    |
    | An array of connections. A single connection definition may be used by
    | one or more sessions (below).
    */
    'connections' => [
        //
        // Each entry in 'connections' is a configuration array for
        // Korowai\Lib\Ldap\Ldap::createWithConfig(). See documentation at
        // https://korowai-framework.readthedocs.io/en/latest/lib/ldap/config.html
        //
        'ldap-service' => [
            'uri' => 'ldap://ldap-service',
            'options' => [
                'protocol_version' => 3
            ]
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Connections
    |--------------------------------------------------------------------------
    | A list of predefined session templates.
    */
    'sessions' => [
        'ldap-service-admin' => [
            'connection' => 'ldap-service',
            'bind' => ['cn=admin,dc=example,dc=org', 'admin'],
            'base_dn' => 'dc=example,dc=org'
        ],
        'ldap-service-anon' => [
            'connection' => 'ldap-service',
            'base_dn' => 'dc=example,dc=org'
        ]
    ]
];

// vim: syntax=php sw=4 ts=4 et:
