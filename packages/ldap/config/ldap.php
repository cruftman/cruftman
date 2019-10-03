<?php
/**
 * @file config/ldap.php
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Connection templates
    |--------------------------------------------------------------------------
    |
    | An array of named connections.
    */
    'connections' => [
        //
        // https://korowai-framework.readthedocs.io/en/latest/lib/ldap/config.html
        //
        'ldap-service' => [
            'uri' => 'ldap://ldap-service'
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */
    'auth' => [
        'contexts' => [
            'defaults' => [
                'method' => 'bind',
                'connection' => 'ldap-service',
                'search' => [
                    'filter' => '(&(uid={{ username }})(accountStatus=enabled)(enabledService=cruftman))',
                    'options' => [
                        'scope' => 'one',
                        'attributes' => []
                    ]
                ],
            ],
            'example.org' => [
                'inherit' => ['defaults'],
                'bind' => [
                    'cn=cruftUserAuth,cn=cruftman.example.org,ou=servers,ou=systems,ou=london,dc=example,dc=org',
                    'london'
                ],
                'search' => [
                    'base_dn' => 'ou=people,dc=example,dc=org'
                ]
            ],
            'london.example.org' => [
                'inherit' => ['defaults'],
                'bind' => [
                    'cn=cruftUserAuth,cn=cruftman.example.org,ou=servers,ou=systems,ou=london,dc=example,dc=org',
                    'london'
                ],
                'search' => [
                    'base_dn' => 'ou=people,ou=london,dc=example,dc=org'
                ]
            ],
            'manchester.example.org' => [
                'inherit' => ['defaults'],
                'bind' => [
                    'cn=cruftUserAuth,cn=cruftman.example.org,ou=servers,ou=systems,ou=manchester,dc=example,dc=org',
                    'manchester'
                ],
                'search' => [
                    'base_dn' => 'ou=people,ou=manchester,dc=example,dc=org'
                ]
            ],
        ],
        'order' => [
            'example.org', 'london.example.org', 'manchester.example.org'
        ]
    ]
];

// vim: syntax=php sw=4 ts=4 et:
